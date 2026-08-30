<div x-show="drawerOpen" x-cloak
    class="fixed inset-0 z-50 md:hidden" x-transition.opacity>
    <div class="absolute inset-0 bg-slate-900/50" @click="drawerOpen = false"></div>
    <aside class="absolute inset-y-0 left-0 w-72 max-w-[85%] bg-white shadow-xl" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
        <div class="flex h-16 items-center justify-between border-b border-slate-100 px-5">
            <div class="flex items-center gap-2.5">
                <x-brand-logo />
                <p class="font-heading text-[15px] font-bold text-slate-900">{{ setting('nama_toko', 'Service Computer') }}</p>
            </div>
            <button class="btn-icon" @click="drawerOpen = false" aria-label="Tutup menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="flex h-[calc(100%-4rem)] flex-col overflow-y-auto p-4">
            <nav class="space-y-1">
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
                <x-nav.link href="{{ route('invoices.index') }}" :active="request()->routeIs('invoices*')">
                    <x-slot:icon><i class="fa-solid fa-file-invoice-dollar fa-fw"></i></x-slot:icon>
                    Invoice
                </x-nav.link>
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
        </div>
    </aside>
</div>