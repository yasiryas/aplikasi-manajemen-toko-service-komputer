<?php

namespace App\Http\Controllers;

use App\Enums\ServiceOrderStatus;
use App\Models\Device;
use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (auth()->user()->isUser()) {
            return redirect()->route('service-orders.progress');
        }

        $statusCounts = $this->statusCounts();

        return view('dashboard', [
            'cards' => $this->cards($statusCounts),
            'recentOrders' => $this->recentOrders(),
            'activity' => $this->activityItems(),
            'statuses' => $this->statusSummary($statusCounts),
            'technicians' => User::teknisi()->get(),
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json($this->cards($this->statusCounts()));
    }

    public function activity(): JsonResponse
    {
        return response()->json(['items' => $this->activityItems()]);
    }

    /**
     * Batasi data ke tiket milik akun user (role customer).
     */
    private function scopeForRole(Builder $query): Builder
    {
        return $query->when(auth()->user()->isUser(), fn (Builder $q) => $q
            ->whereHas('device.customer', fn (Builder $c) => $c->where('user_id', auth()->id())));
    }

    /**
     * @return array<string, int> counts per status value
     */
    private function statusCounts(): array
    {
        return $this->scopeForRole(ServiceOrder::query())
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }

    /**
     * @param  array<string, int>  $countsByStatus
     * @return array<string, int|float|string>
     */
    private function cards(array $countsByStatus): array
    {
        $count = fn (ServiceOrderStatus $status) => (int) ($countsByStatus[$status->value] ?? 0);

        $activeStatuses = array_map(fn ($s) => $s->value, ServiceOrderStatus::active());
        $tiketAktif = array_sum(array_intersect_key($countsByStatus, array_flip($activeStatuses)));

        $orders = $this->scopeForRole(ServiceOrder::query());

        return [
            'tiket_aktif' => $tiketAktif,
            'menunggu_sparepart' => $count(ServiceOrderStatus::MenungguSparepart),
            'selesai_hari_ini' => (clone $orders)
                ->whereIn('status', [ServiceOrderStatus::Selesai, ServiceOrderStatus::Diambil])
                ->whereDate('tanggal_selesai', today())
                ->count(),
            'pendapatan_hari_ini' => Invoice::when(auth()->user()->isUser(), fn ($query) => $query->whereHas('serviceOrder.device.customer', fn ($c) => $c->where('user_id', auth()->id())))
                ->where('status_bayar', 'lunas')->whereDate('created_at', today())->sum('total_biaya'),
        ];
    }

    /**
     * @return Collection<int, ServiceOrder>
     */
    private function recentOrders(): Collection
    {
        return $this->scopeForRole(ServiceOrder::query())
            ->with('device.customer')
            ->latest()
            ->limit(8)
            ->get();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function activityItems(): array
    {
        return DB::table('service_logs as sl')
            ->join('service_orders as so', 'so.id', '=', 'sl.service_order_id')
            ->join('users as u', 'u.id', '=', 'sl.changed_by')
            ->select('sl.service_order_id', 'so.no_tiket', 'sl.status', 'sl.created_at', 'u.name as user')
            ->when(auth()->user()->isUser(), fn ($query) => $query
                ->whereIn('so.device_id', Device::whereHas('customer', fn ($c) => $c->where('user_id', auth()->id()))->pluck('id')))
            ->latest('sl.created_at')
            ->limit(10)
            ->get()
            ->map(fn ($log) => [
                'no_tiket' => $log->no_tiket,
                'status' => $log->status,
                'status_label' => ServiceOrderStatus::tryFrom($log->status)?->label() ?? $log->status,
                'badge_class' => ServiceOrderStatus::tryFrom($log->status)?->badgeClass() ?? 'bg-slate-100 text-slate-700',
                'user' => $log->user,
                'created_at' => Carbon::parse($log->created_at)->diffForHumans(),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<string, int>  $countsByStatus
     * @return array<string, array{total: int, class: string}>
     */
    private function statusSummary(array $countsByStatus): array
    {
        $summary = [];

        foreach (ServiceOrderStatus::cases() as $status) {
            $summary[$status->label()] = [
                'total' => (int) ($countsByStatus[$status->value] ?? 0),
                'class' => $status->progressClass(),
            ];
        }

        return $summary;
    }
}
