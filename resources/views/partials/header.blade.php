<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center gap-3 px-4">
        <button class="btn-icon md:hidden" @click="sidebarOpen = true" aria-label="Buka menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <button class="btn-icon hidden md:inline-flex" @click="toggleSidebar()" aria-label="Kolaps sidebar" title="Kolaps sidebar">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>

        <div class="hidden md:flex md:items-center md:gap-2 md:text-sm">
            <p class="text-lg font-bold text-slate-900">@yield('page-title', 'Dashboard')</p>
        </div>

        <p class="ml-auto hidden text-sm text-slate-500 sm:block">
            {{ \Illuminate\Support\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
        </p>

        @if (auth()->user()->isAdmin())
        <x-dropdown align="right" width="w-72">
            <x-slot:trigger>
                <button class="btn-icon" aria-label="Notifikasi">
                    <i class="fa-solid fa-bell"></i>
                </button>
            </x-slot:trigger>
            <p class="px-2 py-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400">Notifikasi</p>
            <a href="{{ route('invoices.index') }}?status=belum_lunas" class="block rounded-lg px-2 py-2 text-sm text-slate-600 hover:bg-slate-50">
                <span class="font-medium text-slate-900">Ada invoice belum lunas</span><br>
                <span class="text-xs text-slate-400">Lihat invoice menunggu pembayaran</span>
            </a>
        </x-dropdown>
        @endif

        <x-dropdown align="right" width="w-44" class="md:hidden">
            <x-slot:trigger>
                <button class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700" aria-label="Menu akun">
                    {{ strtoupper(Str::substr(auth()->user()->name, 0, 1)) }}
                </button>
            </x-slot:trigger>
            <p class="px-2 py-1.5 text-xs text-slate-500">{{ auth()->user()->name }} · {{ auth()->user()->roleLabel() }}</p>
            <a href="{{ route('dokumentasi') }}" class="mt-1 block border-t border-slate-100 px-2 py-2 text-sm text-slate-600 hover:bg-slate-50">Dokumentasi</a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('settings.edit') }}" class="block px-2 py-2 text-sm text-slate-600 hover:bg-slate-50">Pengaturan</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 pt-1" onsubmit="RepairStation.confirmLogout(event, this)">
                @csrf
                <button type="submit" class="w-full rounded-md px-2 py-2 text-left text-sm text-rose-600 hover:bg-rose-50">Keluar</button>
            </form>
        </x-dropdown>
    </div>
</header>