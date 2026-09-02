@extends('layouts.app', ['page' => 'invoices'])

@section('page-title', 'Invoice')

@section('content')
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Invoice</h1>
            <p class="mt-0.5 text-sm text-slate-500"><span id="invoice-count">{{ $invoices->total() }}</span> invoice terdaftar.</p>
        </div>
        @if (auth()->user()->isAdmin())
            <button type="button" class="btn-primary md:self-auto" onclick="RepairStation.openInvoiceModal()">
                <i class="fa-solid fa-plus"></i>
                Invoice Baru
            </button>
        @endif
    </div>

    <form method="GET" action="{{ route('invoices.index') }}" class="mt-5 flex max-w-md gap-2">
        <input type="search" id="invoice-search" name="q" value="{{ $search }}" class="input" placeholder="Cari no. invoice / tiket / pelanggan&hellip;" autocomplete="off">
        <button type="submit" class="btn-primary shrink-0">Cari</button>
    </form>

    <div class="mt-5 flex gap-2 overflow-x-auto pb-1">
        <a href="{{ route('invoices.index') }}" class="badge shrink-0 px-3 py-1.5 text-sm {{ $currentStatus === '' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }}">Semua</a>
        <a href="{{ route('invoices.index', ['status' => 'belum_lunas']) }}" class="badge shrink-0 px-3 py-1.5 text-sm {{ $currentStatus === 'belum_lunas' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }}">Belum Lunas</a>
        <a href="{{ route('invoices.index', ['status' => 'lunas']) }}" class="badge shrink-0 px-3 py-1.5 text-sm {{ $currentStatus === 'lunas' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }}">Lunas</a>
    </div>

    <div id="invoice-results">
        @include('invoices.partials.list', ['invoices' => $invoices])
    </div>
@endsection