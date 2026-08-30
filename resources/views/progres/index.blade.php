@extends('layouts.app', ['page' => 'progres'])

@section('page-title', 'Progres Servis')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Progres Servis</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau status perbaikan perangkat Anda secara langsung.</p>
        </div>
    </div>

    @if ($orders->isEmpty())
        <div class="card mt-6 p-14 text-center">
            <i class="fa-solid fa-box-open mb-3 text-4xl text-slate-300"></i>
            <p class="text-sm font-medium text-slate-600">Belum ada tiket service</p>
            <p class="mt-1 text-xs text-slate-400">Jika Anda menitipkan perangkat, hubungi toko agar tiket bisa muncul di sini.</p>
        </div>
    @endif

    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        @foreach ($orders as $order)
            <div class="card flex flex-col">
                <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4">
                    <div class="min-w-0">
                        <p class="text-xs font-mono text-slate-400">No. Tiket</p>
                        <p class="font-heading text-lg font-bold text-slate-900">{{ $order->no_tiket }}</p>
                        <p class="mt-1 truncate text-sm text-slate-600">
                            {{ $order->device->jenis->label() }} {{ $order->device->merk }} {{ $order->device->model }}
                        </p>
                        <p class="text-xs text-slate-400">{{ $order->device->keluhan }}</p>
                    </div>
                    <span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
                </div>

                @php
                    $percent = match ($order->status) {
                        \App\Enums\ServiceOrderStatus::Antri => 15,
                        \App\Enums\ServiceOrderStatus::Dikerjakan => 45,
                        \App\Enums\ServiceOrderStatus::MenungguSparepart => 45,
                        \App\Enums\ServiceOrderStatus::Selesai => 80,
                        \App\Enums\ServiceOrderStatus::Diambil => 100,
                    };
                @endphp
                <div class="py-4">
                    <div class="mb-1.5 flex justify-between text-xs text-slate-500">
                        <span>Progres pengerjaan</span>
                        <span class="font-mono">{{ $percent }}%</span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-indigo-500 transition-all duration-700" style="width: {{ $percent }}%"></div>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    @forelse ($order->logs as $log)
                        <div class="flex gap-3">
                            <i class="fa-solid fa-circle-check mt-1 text-xs text-emerald-500"></i>
                            <div class="min-w-0">
                                <p class="text-slate-700">{{ $log->status instanceof \App\Enums\ServiceOrderStatus ? $log->status->label() : $log->status }}</p>
                                @if ($log->catatan && $log->catatan !== 'Tiket dibuat')
                                    <p class="text-xs text-slate-400">{{ $log->catatan }}</p>
                                @endif
                                <p class="text-xs text-slate-400">{{ \Illuminate\Support\Carbon::parse($log->created_at)->diffForHumans() }} oleh {{ $log->changedBy?->name }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada riwayat pengerjaan.</p>
                    @endforelse
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4 text-sm">
                    <div>
                        <p class="text-xs text-slate-400">Teknisi</p>
                        <p class="font-medium text-slate-700">{{ $order->teknisi?->name ?? 'Belum ditentukan' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-400">Masuk</p>
                        <p class="font-medium text-slate-700">{{ tanggal($order->tanggal_masuk) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@endsection