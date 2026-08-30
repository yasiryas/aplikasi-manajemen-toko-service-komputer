<?php

namespace App\Http\Controllers;

use App\Enums\NotificationStatus;
use App\Enums\ServiceOrderStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreServiceOrderRequest;
use App\Http\Requests\UpdateServiceOrderRequest;
use App\Models\ServiceLog;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceOrderController extends Controller
{
    public function __construct(private readonly WhatsAppNotificationService $notificationService) {}

    public function index(Request $request): View
    {
        $status = $request->has('status') ? ServiceOrderStatus::tryFrom($request->string('status')->toString()) : null;

        $orders = ServiceOrder::query()
            ->with(['device.customer', 'teknisi', 'invoice'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($request->user()->role === UserRole::Teknisi, fn ($query) => $query
                ->where(fn ($q) => $q->where('teknisi_id', $request->user()->id)->orWhereNull('teknisi_id')))
            ->when($request->user()->isUser(), fn ($query) => $query
                ->whereHas('device.customer', fn ($q) => $q->where('user_id', $request->user()->id)))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('service-orders.index', [
            'orders' => $orders,
            'currentStatus' => $status,
            'statuses' => ServiceOrderStatus::cases(),
            'technicians' => User::where('role', 'teknisi')->get(),
        ]);
    }

    public function progress(Request $request): View|RedirectResponse
    {
        if ($request->user()->isStaff()) {
            return redirect()->route('service-orders.index');
        }

        $orders = ServiceOrder::query()
            ->with(['device.customer', 'teknisi', 'logs' => fn ($query) => $query->with('changedBy')->latest()])
            ->whereHas('device.customer', fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest()
            ->paginate(12);

        return view('progres.index', [
            'page' => 'progres',
            'pageTitle' => 'Progres Servis',
            'orders' => $orders,
        ]);
    }

    public function store(StoreServiceOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['no_tiket'] = ServiceOrder::generateTicketNumber();

        $order = ServiceOrder::create($data);

        ServiceLog::create([
            'service_order_id' => $order->id,
            'status' => $order->status->value,
            'catatan' => 'Tiket dibuat',
            'changed_by' => $request->user()->id,
        ]);

        return response()->json(['order' => $order->load('device.customer', 'teknisi')]);
    }

    public function show(ServiceOrder $order): JsonResponse
    {
        $this->authorize('view', $order);

        $order->load([
            'device.customer',
            'teknisi',
            'invoice.items',
            'logs' => fn ($query) => $query->with('changedBy')->latest(),
            'notificationLogs' => fn ($query) => $query->latest(),
        ]);

        return response()->json(['order' => $order]);
    }

    public function update(UpdateServiceOrderRequest $request, ServiceOrder $order): JsonResponse
    {
        $this->authorize('update', $order);

        $previousStatus = $order->status;

        $order->update($request->validated());

        if ($previousStatus !== $order->status) {
            $this->recordStatusChange($request, $order, $previousStatus->label(), $order->status->label());
        }

        return response()->json(['order' => $order->load('device.customer', 'teknisi')]);
    }

    public function changeStatus(Request $request, ServiceOrder $order): JsonResponse
    {
        $this->authorize('changeStatus', $order);

        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::enum(ServiceOrderStatus::class)],
            'catatan' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $previousStatus = $order->status;
        $newStatus = ServiceOrderStatus::from($validator->validated()['status']);

        $order->update([
            'status' => $newStatus,
            'tanggal_selesai' => in_array($newStatus, [ServiceOrderStatus::Selesai, ServiceOrderStatus::Diambil], true)
                ? now()->toDateString()
                : $order->tanggal_selesai,
        ]);

        $this->recordStatusChange($request, $order, $previousStatus->label(), $newStatus->label(), $validator->validated()['catatan'] ?? null);

        if ($newStatus === ServiceOrderStatus::Selesai) {
            $this->notificationService->notify($order);
        }

        return response()->json(['order' => $order->fresh(['device.customer', 'teknisi'])]);
    }

    public function destroy(ServiceOrder $order): JsonResponse
    {
        $this->authorize('delete', $order);

        $order->delete();

        return response()->json(['message' => 'Tiket dihapus.']);
    }

    public function notify(ServiceOrder $order): JsonResponse
    {
        $this->authorize('notify', $order);

        $log = $this->notificationService->notify($order);

        return response()->json([
            'message' => $log->status === NotificationStatus::Terkirim
                ? 'Notifikasi berhasil dikirim ulang.'
                : 'Notifikasi gagal dikirim.',
            'log' => $log,
        ]);
    }

    private function recordStatusChange(Request $request, ServiceOrder $order, string $from, string $to, ?string $catatan = null): void
    {
        ServiceLog::create([
            'service_order_id' => $order->id,
            'status' => $order->status->value,
            'catatan' => "{$from} → {$to}".($catatan ? " | {$catatan}" : ''),
            'changed_by' => $request->user()->id,
        ]);
    }
}
