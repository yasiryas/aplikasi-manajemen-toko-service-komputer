@extends('layouts.app', ['page' => 'service-orders'])

@section('page-title', 'Tiket Service')

@section('content')
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Tiket Service</h1>
            <p class="mt-0.5 text-sm text-slate-500">Pantau dan kelola antrian perbaikan.</p>
        </div>
        @if (auth()->user()->isStaff())
            <button type="button" class="btn-primary md:self-auto" onclick="openModal('modal-order-form')">
                <i class="fa-solid fa-plus"></i>
                Tiket Baru
            </button>
        @endif
    </div>

    @error('status')
        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
    @enderror

    {{-- Filter status pill --}}
    <div class="mt-5 flex gap-2 overflow-x-auto pb-1">
        <a href="{{ route('service-orders.index') }}" class="badge shrink-0 px-3 py-1.5 text-sm {{ is_null($currentStatus) ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }}">Semua</a>
        @foreach ($statuses as $status)
            <a href="{{ route('service-orders.index', ['status' => $status->value]) }}" class="badge shrink-0 px-3 py-1.5 text-sm {{ $currentStatus === $status ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }}">
                {{ $status->label() }}
            </a>
        @endforeach
    </div>

    @if ($orders->isEmpty())
        <div class="card mt-5 p-16 text-center">
            <p class="text-sm text-slate-400">Tidak ada tiket{{ $currentStatus ? ' berstatus '.$currentStatus->label() : '' }}.</p>
            @if (auth()->user()->isStaff())
            <button type="button" class="btn-primary mt-4" onclick="openModal('modal-order-form')">Buat Tiket Baru</button>
        @else
            <p class="mt-2 text-xs text-slate-400">Anda hanya dapat melihat tiket. Hubungi admin atau teknisi untuk membuat tiket.</p>
        @endif
        </div>
    @else
        {{-- Tabel desktop --}}
        <div class="card mt-5 hidden overflow-hidden lg:block">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Tiket</th>
                        <th class="px-4 py-3 font-semibold">Pelanggan</th>
                        <th class="px-4 py-3 font-semibold">Perangkat</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Estimasi</th>
                        <th class="px-4 py-3 font-semibold">Teknisi</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($orders as $order)
                        @include('service-orders.partials.ticket-row', ['order' => $order])
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Kartu mobile --}}
        <div class="mt-4 space-y-3 lg:hidden">
            @foreach ($orders as $order)
                @include('service-orders.partials.ticket-card', ['order' => $order])
            @endforeach
        </div>

        <div class="mt-5">
            {{ $orders->links() }}
        </div>
    @endif

    @push('modals')
        @if (auth()->user()->isStaff())
        <x-modal id="modal-order-form" title="Tiket Baru" maxWidth="md:max-w-2xl">
            @include('service-orders.partials.order-form')
        </x-modal>
    @endif

    <x-modal id="modal-order-detail" title="Detail Tiket" maxWidth="md:max-w-2xl">
            <div id="order-detail-body"><p class="py-6 text-center text-sm text-slate-400">Memuat&hellip;</p></div>
        </x-modal>
    @endpush

    @if (auth()->user()->isStaff())
    <button type="button" class="btn-fab md:hidden" onclick="openModal('modal-order-form')" aria-label="Tiket baru">
        <i class="fa-solid fa-plus"></i>
    </button>
@endif
@endsection