<?php

namespace App\Http\Controllers;

use App\Enums\ServiceOrderStatus;
use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'cards' => $this->cards(),
            'recentOrders' => $this->recentOrders(),
            'activity' => $this->activityItems(),
            'statuses' => $this->statusSummary(),
            'technicians' => User::where('role', 'teknisi')->get(),
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json($this->cards());
    }

    public function activity(): JsonResponse
    {
        return response()->json(['items' => $this->activityItems()]);
    }

    /**
     * @return array<string, int|float|string>
     */
    private function cards(): array
    {
        return [
            'tiket_aktif' => ServiceOrder::whereIn('status', array_map(fn ($s) => $s->value, ServiceOrderStatus::active()))->count(),
            'menunggu_sparepart' => ServiceOrder::where('status', ServiceOrderStatus::MenungguSparepart)->count(),
            'selesai_hari_ini' => ServiceOrder::whereIn('status', [ServiceOrderStatus::Selesai, ServiceOrderStatus::Diambil])
                ->whereDate('tanggal_selesai', today())
                ->count(),
            'pendapatan_hari_ini' => Invoice::where('status_bayar', 'lunas')->whereDate('created_at', today())->sum('total_biaya'),
        ];
    }

    /**
     * @return Collection<int, ServiceOrder>
     */
    private function recentOrders(): Collection
    {
        return ServiceOrder::query()
            ->with(['device.customer', 'teknisi', 'invoice'])
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
     * @return array<string, array{total: int, class: string}>
     */
    private function statusSummary(): array
    {
        $counts = ServiceOrder::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $summary = [];

        foreach (ServiceOrderStatus::cases() as $status) {
            $summary[$status->label()] = [
                'total' => (int) $counts->get($status->value, 0),
                'class' => $status->progressClass(),
            ];
        }

        return $summary;
    }
}
