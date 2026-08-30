@extends('layouts.app', ['page' => 'customers'])

@section('page-title', 'Pelanggan')

@section('content')
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Pelanggan</h1>
            <p class="mt-0.5 text-sm text-slate-500">{{ $customers->total() }} pelanggan terdaftar.</p>
        </div>
        @if (auth()->user()->isAdmin())
            <button type="button" class="btn-primary md:self-auto" onclick="RepairStation.openCustomerForm()">
                <i class="fa-solid fa-user-plus"></i>
                Pelanggan Baru
            </button>
        @endif
    </div>

    <form method="GET" action="{{ route('customers.index') }}" class="mt-5 flex max-w-md gap-2">
        <input type="search" name="q" value="{{ $search }}" class="input" placeholder="Cari nama / no. HP&hellip;">
        <button type="submit" class="btn-primary shrink-0">Cari</button>
    </form>

    <div class="card mt-5 overflow-hidden">
        @if ($customers->isEmpty())
            <p class="py-14 text-center text-sm text-slate-400">Belum ada pelanggan.</p>
        @else
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nama</th>
                        <th class="px-4 py-3 font-semibold">No. HP</th>
                        <th class="px-4 py-3 font-semibold">Perangkat</th>
                        <th class="px-4 py-3 font-semibold">Alamat</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($customers as $customer)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <a href="{{ route('customers.show', $customer) }}" class="font-medium text-slate-900 hover:text-indigo-600">{{ $customer->nama }}</a>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $customer->no_hp }}</td>
                            <td class="px-4 py-3"><span class="badge bg-indigo-100 text-indigo-700">{{ $customer->devices_count }} perangkat</span></td>
                            <td class="max-w-[200px] truncate px-4 py-3 text-slate-500">{{ $customer->alamat ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('customers.show', $customer) }}" class="btn-icon h-9 w-9 text-slate-500 hover:text-indigo-600" aria-label="Lihat detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if (auth()->user()->isAdmin())
                                        <button type="button" class="btn-icon h-9 w-9 text-slate-500 hover:text-indigo-600" onclick="RepairStation.openCustomerForm({{ $customer->toJson() }})" aria-label="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn-icon h-9 w-9 text-slate-500 hover:text-rose-600" onclick="RepairStation.deleteCustomer({{ $customer->id }})" aria-label="Hapus">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="mt-5">
        {{ $customers->links() }}
    </div>

    @push('modals')
        <x-modal id="modal-customer-form" title="Form Pelanggan">
            @include('customers.partials.customer-form')
        </x-modal>
    @endpush
@endsection