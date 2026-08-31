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
        <title>@yield('title', 'Masuk') - {{ setting('nama_toko', 'Service Computer') }}</title>
        <link rel="icon" href="{{ logo_url() ?? asset('favicon.ico') }}">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-screen items-center justify-center bg-gradient-to-br from-brand-700 via-brand-600 to-violet-700 p-4">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -bottom-32 -right-16 h-96 w-96 rounded-full bg-violet-400/20 blur-3xl"></div>
        </div>

        <div class="relative w-full max-w-sm">
            <div class="rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-white/20 md:p-8">
                <div class="mb-6 text-center">
                    <img src="{{ logo_url() ?? asset('icons/icon-192.png') }}" alt="{{ setting('nama_toko', 'Service Computer') }}" class="mx-auto mb-3 h-16 w-16 rounded-2xl object-contain p-1">
                    <h1 class="font-heading text-2xl font-bold text-slate-900">{{ setting('nama_toko', 'Service Computer') }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ setting('tagline_toko', 'Service Komputer') }}</p>
                </div>

                @yield('content')
            </div>

            <p class="mt-6 text-center text-xs text-brand-100/80">Sistem Manajemen Servis {{ setting('nama_toko', 'Service Computer') }}</p>
        </div>

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js');
                });
            }
        </script>
    </body>
</html>