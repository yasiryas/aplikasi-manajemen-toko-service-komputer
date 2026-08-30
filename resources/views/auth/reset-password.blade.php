@extends('layouts.guest')

@section('title', 'Reset Kata Sandi')

@section('content')
    <h1 class="font-heading text-xl font-bold text-slate-900">Reset Kata Sandi</h1>
    <p class="mt-1 text-sm text-slate-500">Buat kata sandi baru untuk akun Anda.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-rose-50 p-3 text-sm text-rose-700">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label class="label" for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}" class="input" required>
        </div>
        <div>
            <label class="label" for="password">Kata Sandi Baru</label>
            <input type="password" id="password" name="password" class="input" autocomplete="new-password" required>
        </div>
        <div>
            <label class="label" for="password_confirmation">Ulangi Kata Sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="input" required>
        </div>
        <button type="submit" class="btn-primary w-full">Reset Kata Sandi</button>
    </form>
@endsection