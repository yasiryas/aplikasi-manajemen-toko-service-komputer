@extends('layouts.app', ['page' => 'customers'])

@section('page-title', 'Pelanggan')

@section('content')
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Pelanggan</h1>
            <p class="mt-0.5 text-sm text-slate-500"><span id="customer-total">{{ $customers->total() }}</span> pelanggan terdaftar.</p>
        </div>
        @if (auth()->user()->isAdmin())
            <div class="flex flex-wrap gap-2 md:self-auto">
                <a href="{{ route('customers.export', request()->only('status')) }}" class="btn-secondary">
                    <i class="fa-solid fa-file-csv"></i>
                    Ekspor
                </a>
                <button type="button" class="btn-secondary" onclick="RepairStation.openImportModal()">
                    <i class="fa-solid fa-file-import"></i>
                    Import
                </button>
                <button type="button" class="btn-primary" onclick="RepairStation.openCustomerForm()">
                    <i class="fa-solid fa-user-plus"></i>
                    Pelanggan Baru
                </button>
            </div>
        @endif
    </div>

    <div class="mt-5 flex gap-2 overflow-x-auto pb-1">
        <a href="{{ route('customers.index') }}" class="badge shrink-0 px-3 py-1.5 text-sm {{ $currentStatus === '' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }}">Aktif</a>
        <a href="{{ route('customers.index', ['status' => 'arsip']) }}" class="badge shrink-0 px-3 py-1.5 text-sm {{ $currentStatus === 'arsip' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }}">Arsip</a>
    </div>

    <form method="GET" action="{{ route('customers.index') }}" class="mt-5 flex max-w-md gap-2">
        <input type="search" id="customer-search" name="q" value="{{ $search }}" class="input" placeholder="Cari nama / no. HP&hellip;" autocomplete="off">
        <button type="submit" class="btn-primary shrink-0">Cari</button>
    </form>

    <div id="customer-results" class="card mt-5 overflow-hidden">
        @include('customers.partials.table', ['customers' => $customers, 'archived' => $currentStatus === 'arsip'])
    </div>

    <div id="customer-pagination" class="mt-5">
        {{ $customers->links() }}
    </div>

    @push('modals')
        <x-modal id="modal-customer-form" title="Form Pelanggan">
            @include('customers.partials.customer-form')
        </x-modal>

        <x-modal id="modal-customer-detail" title="Detail Pelanggan" maxWidth="md:max-w-2xl">
            <div id="customer-detail-body"><p class="py-6 text-center text-sm text-slate-400">Memuat&hellip;</p></div>
        </x-modal>

        @if (auth()->user()->isAdmin())
        <x-modal id="modal-customer-import" title="Import Pelanggan">
            <form id="customer-import-form" enctype="multipart/form-data" class="space-y-4">
                <p class="text-sm text-slate-500">File CSV dengan kolom: <span class="font-mono text-xs text-slate-700">Nama, No HP, Alamat</span> (baris pertama header). Pelanggan dengan No HP yang sama akan diperbarui.</p>
                <input type="file" id="customer-import-file" accept=".csv" class="input" required>
                <div id="customer-import-errors" class="hidden rounded-lg bg-rose-50 p-3 text-sm text-rose-700"></div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="btn-secondary" data-close-modal>Batal</button>
                    <button type="submit" class="btn-primary">Import</button>
                </div>
            </form>
        </x-modal>
        @endif
    @endpush
@endsection