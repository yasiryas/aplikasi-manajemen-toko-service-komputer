@php
    $user = auth()->user();
    if ($user->isUser()) {
        $items = [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fa-gauge-high'],
            ['label' => 'Progres', 'route' => 'service-orders.progress', 'icon' => 'fa-arrow-trend-up'],
            ['label' => 'Dokumentasi', 'route' => 'dokumentasi', 'icon' => 'fa-book'],
        ];
    } else {
        $items = [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fa-gauge-high'],
            ['label' => 'Tiket', 'route' => 'service-orders.index', 'icon' => 'fa-list-check'],
            ['label' => 'Pelanggan', 'route' => 'customers.index', 'icon' => 'fa-users'],
            $user->isAdmin()
                ? ['label' => 'Invoice', 'route' => 'invoices.index', 'icon' => 'fa-file-invoice-dollar']
                : ['label' => 'Perangkat', 'route' => 'devices.index', 'icon' => 'fa-hard-drive'],
        ];
    }
    $cols = count($items);
@endphp
<nav class="fixed inset-x-0 bottom-0 z-40 grid border-t border-slate-200 bg-white px-2 pb-[env(safe-area-inset-bottom)] md:hidden" style="grid-template-columns: repeat({{ $cols }}, 1fr)">
    @foreach ($items as $item)
        <a href="{{ route($item['route']) }}"
            class="flex flex-col items-center gap-1 py-2 text-[11px] font-medium {{ request()->routeIs(Str::before($item['route'], '.') . '*') ? 'text-indigo-600' : 'text-slate-500' }}">
            <i class="fa-solid {{ $item['icon'] }} text-lg"></i>
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>