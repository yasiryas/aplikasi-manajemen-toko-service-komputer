<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#4f46e5">
        <link rel="manifest" href="/manifest.webmanifest">
        <link rel="apple-touch-icon" href="/icons/icon-192.png">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <title>{{ setting('nama_toko', 'Service Computer') }} — @yield('page-title', 'Dashboard')</title>
        <link rel="icon" href="{{ logo_url() ?? asset('icons/icon-192.png') }}">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body data-page="{{ $page ?? '' }}" data-admin="{{ auth()->user()->isAdmin() ? 1 : 0 }}" data-staff="{{ auth()->user()->isStaff() ? 1 : 0 }}">
        <div
            x-data="{
                sidebarOpen: false,
                sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === '1',
                toggleSidebar() {
                    this.sidebarCollapsed = ! this.sidebarCollapsed;
                    localStorage.setItem('sidebar-collapsed', this.sidebarCollapsed ? '1' : '0');
                },
            }"
            class="min-h-screen bg-slate-100"
        >
            {{-- Sidebar: off-canvas di mobile, kolaps ala SB Admin di desktop --}}
            @include('partials.sidebar')

            <div :class="sidebarCollapsed ? 'md:pl-16' : 'md:pl-64'" class="flex min-h-screen flex-col transition-[padding] duration-200">
                @include('partials.header')

                <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 pb-28 md:pb-10">
                    @yield('content')
                </main>
            </div>

            {{-- Bottom navigation mobile --}}
            @include('partials.bottom-nav')
        </div>

        @if (auth()->user()->isAdmin())
            @include('invoices.partials.form-modal')
        @endif

        @stack('modals')
        @stack('scripts')

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js');
                });
            }
        </script>
    </body>
</html>