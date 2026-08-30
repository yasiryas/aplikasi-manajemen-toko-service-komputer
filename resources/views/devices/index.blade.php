@extends('layouts.app', ['page' => 'devices'])

@section('page-title', 'Perangkat')

@section('content')
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Perangkat</h1>
            <p class="mt-0.5 text-sm text-slate-500">Semua perangkat yang pernah diservice.</p>
        </div>
        @if (auth()->user()->isStaff())
            <button type="button" class="btn-primary md:self-auto" onclick="openModal('modal-device-form')">
                <i class="fa-solid fa-plus"></i>
                Perangkat Baru
            </button>
        @endif
    </div>

    <form method="GET" action="{{ route('devices.index') }}" class="mt-5 flex max-w-md gap-2">
        <input type="search" name="q" value="{{ $search }}" class="input" placeholder="Cari merk / model&hellip;">
        <button type="submit" class="btn-primary shrink-0">Cari</button>
    </form>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($devices as $device)
            <div class="card p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="truncate font-medium text-slate-900">{{ $device->merk }} {{ $device->model }}</p>
                        <a href="{{ route('customers.show', $device->customer) }}" class="text-xs text-indigo-600 hover:text-indigo-700">{{ $device->customer->nama }}</a>
                    </div>
                    <span class="badge bg-slate-100 text-slate-600">{{ $device->jenis->label() }}</span>
                </div>
                <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $device->keluhan }}</p>
                <div class="mt-3 flex justify-end gap-1.5 border-t border-slate-100 pt-2.5">
                @if (auth()->user()->isAdmin())
                    <button type="button" class="btn-icon h-9 w-9 text-slate-500 hover:text-indigo-600" onclick="RepairStation.openDeviceForm({{ $device->customer_id }}, {{ $device->toJson() }})" aria-label="Edit">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button type="button" class="btn-icon h-9 w-9 text-slate-500 hover:text-rose-600" onclick="RepairStation.deleteDevice({{ $device->id }})" aria-label="Hapus">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endif
            </div>
            </div>
        @empty
            <p class="col-span-full py-14 text-center text-sm text-slate-400">Belum ada perangkat terdaftar.</p>
        @endforelse
    </div>

    <div class="mt-5">
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