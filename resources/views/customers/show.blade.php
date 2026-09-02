@extends('layouts.app', ['page' => 'customers'])

@section('page-title', $customer->nama)

@section('content')
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('customers.index') }}" class="hover:text-indigo-600">Pelanggan</a>
        <span>&rarr;</span>
        <span class="text-slate-900">{{ $customer->nama }}</span>
    </div>

    <div class="card mt-4 p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100 text-lg font-semibold text-indigo-700">
                    {{ strtoupper(Str::substr($customer->nama, 0, 1)) }}
                </div>
                <div>
                    <h1 class="font-heading text-xl font-bold text-slate-900">{{ $customer->nama }}</h1>
                    <p class="font-mono text-sm text-slate-500">{{ $customer->no_hp }}</p>
                    @if ($customer->alamat)
                        <p class="text-sm text-slate-500">{{ $customer->alamat }}</p>
                    @endif
                </div>
            </div>
            <span class="badge bg-indigo-100 text-indigo-700">{{ $customer->devices_count }} perangkat · {{ $customer->devices->sum(fn ($d) => $d->service_orders_count) }} tiket</span>
        </div>
    </div>

    <div class="mt-6 flex items-center justify-between">
        <h2 class="font-heading text-base font-bold text-slate-900">Riwayat Perangkat &amp; Tiket</h2>
        @if (auth()->user()->isStaff())
            <button type="button" class="btn-primary" onclick="RepairStation.openDeviceForm({{ $customer->id }})">
                <i class="fa-solid fa-plus"></i>
                Perangkat Baru
            </button>
        @endif
    </div>

    <div class="mt-3 grid gap-4 md:grid-cols-2">
        @foreach ($customer->devices as $device)
            <div class="card p-4">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-medium text-slate-900">{{ $device->merk }} {{ $device->model }}</p>
                        <p class="text-xs text-slate-500">{{ $device->jenis->label() }}</p>
                    </div>
                    <div class="flex gap-1">
                        @if (auth()->user()->isAdmin())
                            <button type="button" class="btn-icon h-8 w-8 text-slate-500 hover:text-indigo-600" onclick="RepairStation.openDeviceForm({{ $customer->id }}, {{ $device->id }})" aria-label="Edit perangkat">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" class="btn-icon h-8 w-8 text-slate-500 hover:text-rose-600" onclick="RepairStation.deleteDevice({{ $device->id }})" aria-label="Hapus perangkat">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        @endif
                    </div>
                </div>
                <p class="mt-2 text-sm text-slate-600"><span class="font-semibold">Keluhan:</span> {{ $device->keluhan }}</p>

                @if ($device->serviceOrders->isEmpty())
                    <p class="mt-3 rounded-lg bg-slate-50 p-2 text-xs text-slate-400">Belum ada tiket service untuk perangkat ini.</p>
                @else
                    <ul class="mt-3 space-y-1.5">
                        @foreach ($device->serviceOrders as $order)
                            <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                                <span class="font-mono text-xs font-semibold text-indigo-600">{{ $order->no_tiket }}</span>
                                @include('partials.status-badge', ['status' => $order->status])
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>

    @push('modals')
        <x-modal id="modal-device-form" title="Form Perangkat">
            @include('devices.partials.device-form')
        </x-modal>

        <x-modal id="modal-customer-detail" title="Detail Pelanggan" maxWidth="md:max-w-2xl">
            <div id="customer-detail-body"><p class="py-6 text-center text-sm text-slate-400">Memuat&hellip;</p></div>
        </x-modal>
    @endpush
@endsection