@extends('layouts.guest')

@section('title', 'Lupa Kata Sandi')

@section('content')
    <h1 class="font-heading text-xl font-bold text-slate-900">Lupa Kata Sandi</h1>
    <p class="mt-1 text-sm text-slate-500">Kami akan mengirim tautan reset ke email Anda.</p>

    @if (session('status'))
        <div class="mt-4 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-rose-50 p-3 text-sm text-rose-700">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="label" for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" class="input" autofocus required>
        </div>
        <button type="submit" class="btn-primary w-full">Kirim Tautan Reset</button>
    </form>
@endsection