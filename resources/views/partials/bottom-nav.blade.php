<nav class="fixed inset-x-0 bottom-0 z-40 grid grid-cols-4 border-t border-slate-200 bg-white px-2 pb-[env(safe-area-inset-bottom)] md:hidden">
    @php
        $items = [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fa-gauge-high'],
            ['label' => 'Tiket', 'route' => 'service-orders.index', 'icon' => 'fa-list-check'],
            ['label' => 'Pelanggan', 'route' => 'customers.index', 'icon' => 'fa-users'],
            ['label' => 'Invoice', 'route' => 'invoices.index', 'icon' => 'fa-file-invoice-dollar'],
        ];
    @endphp
    @foreach ($items as $item)
        <a href="{{ route($item['route']) }}"
            class="flex flex-col items-center gap-1 py-2 text-[11px] font-medium {{ request()->routeIs(Str::before($item['route'], '.') . '*') ? 'text-indigo-600' : 'text-slate-500' }}">
            <i class="fa-solid {{ $item['icon'] }} text-lg"></i>
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>