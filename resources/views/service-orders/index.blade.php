@extends('layouts.app', ['page' => 'service-orders'])

@section('page-title', 'Tiket Service')

@section('content')
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Tiket Service</h1>
            <p class="mt-0.5 text-sm text-slate-500"><span id="order-total">{{ $orders->total() }}</span> tiket terdaftar.</p>
        </div>
        @if (auth()->user()->isStaff())
            <button type="button" class="btn-primary md:self-auto" onclick="openModal('modal-order-form')">
                <i class="fa-solid fa-plus"></i>
                Tiket Baru
            </button>
        @endif
    </div>

    <form method="GET" action="{{ route('service-orders.index') }}" class="mt-5 flex max-w-md gap-2">
        <input type="search" id="order-search" name="q" value="{{ $search }}" class="input" placeholder="Cari no. tiket / pelanggan / perangkat&hellip;" autocomplete="off">
        <button type="submit" class="btn-primary shrink-0">Cari</button>
    </form>

    {{-- Filter status pill --}}
    <div class="mt-5 flex gap-2 overflow-x-auto pb-1">
        <a href="{{ route('service-orders.index') }}" class="badge shrink-0 px-3 py-1.5 text-sm {{ is_null($currentStatus) ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }}">Semua</a>
        @foreach ($statuses as $status)
            <a href="{{ route('service-orders.index', ['status' => $status->value]) }}" class="badge shrink-0 px-3 py-1.5 text-sm {{ $currentStatus === $status ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }}">
                {{ $status->label() }}
            </a>
        @endforeach
    </div>

    <div id="order-results">
        @include('service-orders.partials.list', ['orders' => $orders, 'currentStatus' => $currentStatus])
    </div>

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