@extends('layouts.app', ['page' => 'devices'])

@section('page-title', 'Perangkat')

@section('content')
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Perangkat</h1>
            <p class="mt-0.5 text-sm text-slate-500"><span id="device-total">{{ $devices->total() }}</span> perangkat terdaftar.</p>
        </div>
        @if (auth()->user()->isStaff())
            <button type="button" class="btn-primary md:self-auto" onclick="openModal('modal-device-form')">
                <i class="fa-solid fa-plus"></i>
                Perangkat Baru
            </button>
        @endif
    </div>

    <form method="GET" action="{{ route('devices.index') }}" class="mt-5 flex max-w-md gap-2">
        <input type="search" id="device-search" name="q" value="{{ $search }}" class="input" placeholder="Cari merk / model&hellip;" autocomplete="off">
        <button type="submit" class="btn-primary shrink-0">Cari</button>
    </form>

    <div id="device-results" class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @include('devices.partials.list', ['devices' => $devices])
    </div>

    <div id="device-pagination" class="mt-5">
        {{ $devices->links() }}
    </div>

    @push('modals')
        <x-modal id="modal-device-form" title="Form Perangkat">
            <div id="device-owner-picker" class="mb-4">
                <label class="label" for="device-owner-search">Pelanggan</label>
                <input type="text" id="device-owner-search" class="input" placeholder="Cari nama / no. HP pelanggan&hellip;">
                <ul id="device-owner-results" class="mt-1 hidden divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white text-sm shadow-sm"></ul>
            </div>
            @include('devices.partials.device-form')
        </x-modal>
    @endpush
@endsection