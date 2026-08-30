@extends('layouts.guest')

@section('title', 'Verifikasi Email')

@section('content')
    <h1 class="font-heading text-xl font-bold text-slate-900">Verifikasi Email</h1>
    <p class="mt-1 text-sm text-slate-500">Sebelum melanjutkan, verifikasi email Anda terlebih dahulu. Cek kotak masuk untuk tautan verifikasi.</p>

    @if (session('status') === 'verification-link-sent')
        <div class="mt-4 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-700">Tautan verifikasi baru telah dikirim ke email Anda.</div>
    @endif

    <div class="mt-6 space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary w-full">Kirim Ulang Tautan Verifikasi</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-secondary w-full">Keluar</button>
        </form>
    </div>
@endsection