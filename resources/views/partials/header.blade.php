<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center gap-3 px-4">
        <button class="btn-icon md:hidden" @click="drawerOpen = true" aria-label="Buka menu">
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

        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button class="btn-icon" @click="open = !open" aria-label="Notifikasi">
                <i class="fa-solid fa-bell"></i>
            </button>
            <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-72 rounded-lg border border-slate-200 bg-white p-2 shadow-lg">
                <p class="px-2 py-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400">Notifikasi</p>
                <a href="{{ route('invoices.index') }}?status=belum_lunas" class="block rounded-lg px-2 py-2 text-sm text-slate-600 hover:bg-slate-50">
                    <span class="font-medium text-slate-900">Ada invoice belum lunas</span><br>
                    <span class="text-xs text-slate-400">Lihat invoice menunggu pembayaran</span>
                </a>
            </div>
        </div>

        <div class="relative md:hidden" x-data="{ open: false }" @click.outside="open = false">
            <button class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700" @click="open = !open" aria-label="Menu akun">
                {{ strtoupper(Str::substr(auth()->user()->name, 0, 1)) }}
            </button>
            <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-44 rounded-lg border border-slate-200 bg-white p-1.5 shadow-lg">
                <p class="px-2 py-1.5 text-xs text-slate-500">{{ auth()->user()->name }} · {{ auth()->user()->isAdmin() ? 'Admin' : 'Teknisi' }}</p>
                <a href="{{ route('dokumentasi') }}" class="mt-1 block border-t border-slate-100 px-2 py-2 text-sm text-slate-600 hover:bg-slate-50">Dokumentasi</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('settings.edit') }}" class="block px-2 py-2 text-sm text-slate-600 hover:bg-slate-50">Pengaturan</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 pt-1" onsubmit="RepairStation.confirmLogout(event, this)">
                    @csrf
                    <button type="submit" class="w-full rounded-md px-2 py-2 text-left text-sm text-rose-600 hover:bg-rose-50">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>