@extends('layouts.app', ['page' => 'dashboard'])

@section('page-title', 'Dashboard')

@section('content')
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Halo, {{ auth()->user()->name }} 👋</h1>
            <p class="mt-0.5 flex items-center gap-1.5 text-xs text-slate-500">
                <span class="inline-block h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                Data realtime · diperbarui otomatis
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('service-orders.index') }}" class="btn-secondary"><i class="fa-solid fa-list-check mr-1.5"></i>Lihat Semua Tiket</a>
            <a href="{{ route('invoices.index') }}" class="btn-secondary"><i class="fa-solid fa-file-invoice-dollar mr-1.5"></i>Invoice</a>
        </div>
    </div>

    {{-- Kartu statistik --}}
    <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4 md:gap-4">
        @include('partials.stat-card', [
            'icon' => 'fa-clock',
            'iconBg' => 'bg-amber-100',
            'accent' => 'text-amber-600',
            'label' => 'Tiket Aktif',
            'value' => '<span data-stat="tiket_aktif">'.$cards['tiket_aktif'].'</span>',
            'hint' => 'Antri · Dikerjakan · Sparepart',
        ])
        @include('partials.stat-card', [
            'icon' => 'fa-cubes',
            'iconBg' => 'bg-rose-100',
            'accent' => 'text-rose-600',
            'label' => 'Menunggu Sparepart',
            'value' => '<span data-stat="menunggu_sparepart">'.$cards['menunggu_sparepart'].'</span>',
            'hint' => 'Perlu suku cadang',
        ])
        @include('partials.stat-card', [
            'icon' => 'fa-circle-check',
            'iconBg' => 'bg-emerald-100',
            'accent' => 'text-emerald-600',
            'label' => 'Selesai Hari Ini',
            'value' => '<span data-stat="selesai_hari_ini">'.$cards['selesai_hari_ini'].'</span>',
            'hint' => 'Siap diambil',
        ])
        @include('partials.stat-card', [
            'icon' => 'fa-sack-dollar',
            'iconBg' => 'bg-indigo-100',
            'accent' => 'text-indigo-600',
            'label' => 'Pendapatan Hari Ini',
            'value' => '<span data-stat="pendapatan_hari_ini">'.rupiah($cards['pendapatan_hari_ini']).'</span>',
            'hint' => 'Invoice lunas',
        ])
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        {{-- Daftar tiket terbaru --}}
        <section class="card p-5 xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-heading text-base font-bold text-slate-900">Tiket Terbaru</h2>
                <a href="{{ route('service-orders.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Kelola &rarr;</a>
            </div>

            @if ($recentOrders->isEmpty())
                <p class="py-10 text-center text-sm text-slate-400">Belum ada tiket service.</p>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($recentOrders as $order)
                        <li class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-3">
                                <a href="{{ route('service-orders.index') }}" class="font-mono text-xs font-semibold text-indigo-600">{{ $order->no_tiket }}</a>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-slate-900">{{ $order->device->customer->nama }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $order->device->merk }} {{ $order->device->model }}</p>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center justify-between gap-3">
                                @include('partials.status-badge', ['status' => $order->status])
                                <span class="text-xs text-slate-400">{{ $order->tanggal_masuk->format('d/m') }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Feed aktivitas realtime --}}
        <section class="card p-5">
            <h2 class="mb-4 font-heading text-base font-bold text-slate-900">Aktivitas Realtime</h2>
            <ol id="activity-feed" class="relative space-y-4 border-l border-slate-200 pl-4">
                @if (empty($activity))
                    <li class="text-sm text-slate-400">Belum ada aktivitas.</li>
                @endif
                @include('partials.activity-item', ['items' => $activity])
            </ol>
        </section>
    </div>

    {{-- Ringkasan status --}}
    <section class="card mt-6 p-5">
        <h2 class="mb-4 font-heading text-base font-bold text-slate-900">Ringkasan Status</h2>
        @php
            $totalOrders = collect($statuses)->sum('total') ?: 1;
        @endphp
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            @foreach ($statuses as $label => $data)
                @include('partials.status-progress', [
                    'label' => $label,
                    'total' => $data['total'],
                    'percent' => $data['total'] / $totalOrders * 100,
                    'class' => $data['class'],
                ])
            @endforeach
        </div>
    </section>

    @push('modals')
        <x-modal id="modal-order-form" title="Form Tiket Service">
            @include('service-orders.partials.order-form')
        </x-modal>
    @endpush

    <button type="button" class="btn-fab md:hidden" onclick="openModal('modal-order-form')" aria-label="Tiket baru">
        <i class="fa-solid fa-plus text-xl"></i>
    </button>
@endsection