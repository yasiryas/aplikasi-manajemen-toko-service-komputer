@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <h2 class="text-lg font-semibold text-slate-900">Selamat datang kembali</h2>
    <p class="mt-1 text-sm text-slate-500">Silakan masuk dengan akun Anda.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-rose-50 p-3 text-sm text-rose-700">
            <i class="fa-solid fa-circle-exclamation mr-1.5"></i>{{ $errors->first() }}
        </div>
    @endif

    @if (session('status'))
        <div class="mt-4 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-700">
            <i class="fa-solid fa-circle-check mr-1.5"></i>{{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="label" for="email">Email</label>
            <div class="relative">
                <i class="fa-solid fa-envelope pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="input !pl-9" placeholder="nama@email.com" autofocus autocomplete="username" required>
            </div>
        </div>
        <div>
            <div class="mb-1 flex items-center justify-between">
                <label class="block text-sm font-medium text-slate-700" for="password">Kata Sandi</label>
                <a href="{{ route('password.request') }}" class="text-xs font-medium text-brand-600 hover:text-brand-700">Lupa?</a>
            </div>
            <div class="relative">
                <i class="fa-solid fa-lock pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="password" id="password" name="password" class="input !pl-9" placeholder="••••••••" autocomplete="current-password" required>
            </div>
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            Ingat saya
        </label>
        <button type="submit" class="btn-primary w-full !bg-brand-600">
            <i class="fa-solid fa-right-to-bracket"></i>
            Masuk
        </button>
    </form>

    @if (setting('demo_mode', '1') === '1')
    <div class="mt-6 rounded-lg bg-slate-50 p-3 text-xs text-slate-500">
        <p class="font-semibold text-slate-600"><i class="fa-solid fa-circle-info mr-1"></i>Akun demo:</p>
        <p class="flex items-center justify-between gap-2">Admin<span class="font-mono text-slate-600">admin@mail.com / admin123</span></p>
        <p class="flex items-center justify-between gap-2">Teknisi<span class="font-mono text-slate-600">teknisi@mail.com / teknisi123</span></p>
        <p class="flex items-center justify-between gap-2">Customer<span class="font-mono text-slate-600">customer@mail.com / user123</span></p>
    </div>
    @endif
@endsection