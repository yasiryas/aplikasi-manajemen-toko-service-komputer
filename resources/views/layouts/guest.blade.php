<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
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
            <div class="mb-6 text-center">
                @php($logo = logo_url())
                @if ($logo)
                    <img src="{{ $logo }}" alt="{{ setting('nama_toko', 'Service Computer') }}" class="mx-auto mb-3 h-16 w-16 rounded-2xl bg-white object-contain p-2 shadow-lg ring-1 ring-black/5">
                @else
                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-brand-600 shadow-lg">
                        <i class="fa-solid fa-microchip text-2xl"></i>
                    </div>
                @endif
                <h1 class="font-heading text-2xl font-bold text-white">{{ setting('nama_toko', 'Service Computer') }}</h1>
                <p class="mt-1 text-sm text-brand-100">{{ setting('tagline_toko', 'Service Komputer') }}</p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-white/20 md:p-8">
                @yield('content')
            </div>

            <p class="mt-6 text-center text-xs text-brand-100/80">Sistem Manajemen Servis {{ setting('nama_toko', 'Service Computer') }}</p>
        </div>
    </body>
</html>