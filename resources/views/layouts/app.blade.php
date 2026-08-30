<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ setting('nama_toko', 'Service Computer') }} — @yield('page-title', 'Dashboard')</title>
        <link rel="icon" href="{{ logo_url() ?? asset('favicon.ico') }}">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body data-page="{{ $page ?? '' }}" data-admin="{{ auth()->user()->isAdmin() ? 1 : 0 }}" data-staff="{{ auth()->user()->isStaff() ? 1 : 0 }}">
        <div
            x-data="{
                drawerOpen: false,
                sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === '1',
                toggleSidebar() {
                    this.sidebarCollapsed = ! this.sidebarCollapsed;
                    localStorage.setItem('sidebar-collapsed', this.sidebarCollapsed ? '1' : '0');
                },
            }"
            class="min-h-screen bg-slate-100"
        >
            {{-- Sidebar desktop (bisa dikolaps, ala SB Admin) --}}
            @include('partials.sidebar')

            {{-- Drawer mobile --}}
            @include('partials.drawer')

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
    </body>
</html>