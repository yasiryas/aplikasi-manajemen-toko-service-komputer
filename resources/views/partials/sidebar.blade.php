<aside
    :class="sidebarCollapsed ? 'md:w-16' : 'md:w-64'"
    class="hidden w-64 shrink-0 flex-col border-r border-slate-200 bg-white md:fixed md:inset-y-0 md:left-0 md:flex md:z-40"
>
    <div :class="sidebarCollapsed ? 'md:justify-center' : 'md:justify-between'"
        class="flex h-16 items-center gap-2.5 border-b border-slate-100 px-4">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2.5">
            <x-brand-logo iconClass="fa-microchip text-lg" />
            <div x-show="!sidebarCollapsed" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak class="min-w-0">
                <p class="font-heading truncate text-[15px] font-bold leading-tight text-slate-900">{{ setting('nama_toko', 'Service Computer') }}</p>
                <p class="truncate text-xs text-slate-500">{{ setting('tagline_toko', 'Service Komputer') }}</p>
            </div>
        </a>
        <button type="button" class="btn-icon h-8 w-8 hidden shrink-0 md:inline-flex" @click="toggleSidebar()" aria-label="Tutup sidebar" title="Tutup sidebar">
            <i class="fa-solid fa-bars-staggered text-sm"></i>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @if (auth()->user()->isUser())
            <x-nav.link href="{{ route('service-orders.progress') }}" :active="request()->routeIs('service-orders.progress')">
                <x-slot:icon><i class="fa-solid fa-arrow-trend-up fa-fw"></i></x-slot:icon>
                Progres Servis
            </x-nav.link>
        @else
            <x-nav.link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard*')">
                <x-slot:icon><i class="fa-solid fa-gauge-high fa-fw"></i></x-slot:icon>
                Dashboard
            </x-nav.link>

            <x-nav.link href="{{ route('customers.index') }}" :active="request()->routeIs('customers*')">
                <x-slot:icon><i class="fa-solid fa-users fa-fw"></i></x-slot:icon>
                Pelanggan
            </x-nav.link>

            <x-nav.link href="{{ route('devices.index') }}" :active="request()->routeIs('devices*')">
                <x-slot:icon><i class="fa-solid fa-hard-drive fa-fw"></i></x-slot:icon>
                Perangkat
            </x-nav.link>

            <x-nav.link href="{{ route('service-orders.index') }}" :active="request()->routeIs('service-orders*')">
                <x-slot:icon><i class="fa-solid fa-list-check fa-fw"></i></x-slot:icon>
                Tiket Service
            </x-nav.link>

            @if (auth()->user()->isAdmin())
                <x-nav.link href="{{ route('invoices.index') }}" :active="request()->routeIs('invoices*')">
                    <x-slot:icon><i class="fa-solid fa-file-invoice-dollar fa-fw"></i></x-slot:icon>
                    Invoice
                </x-nav.link>
            @endif
        @endif

        <x-nav.link href="{{ route('dokumentasi') }}" :active="request()->routeIs('dokumentasi')">
            <x-slot:icon><i class="fa-solid fa-book fa-fw"></i></x-slot:icon>
            Dokumentasi
        </x-nav.link>

        @if (auth()->user()->isAdmin())
            <x-nav.link href="{{ route('settings.edit') }}" :active="request()->routeIs('settings*')">
                <x-slot:icon><i class="fa-solid fa-gear fa-fw"></i></x-slot:icon>
                Pengaturan
            </x-nav.link>
        @endif
    </nav>

    <div class="border-t border-slate-100 p-3">
        <div :class="sidebarCollapsed ? 'md:justify-center' : ''" class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                {{ strtoupper(Str::substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div x-show="!sidebarCollapsed" x-cloak class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-500">{{ auth()->user()->roleLabel() }}</p>
            </div>
            <div x-show="!sidebarCollapsed" x-cloak>
                <form method="POST" action="{{ route('logout') }}" onsubmit="RepairStation.confirmLogout(event, this)">
                    @csrf
                    <button type="submit" class="btn-icon" title="Keluar" aria-label="Keluar">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>