@if ($customers->isEmpty())
    <p class="py-14 text-center text-sm text-slate-400">{{ $archived ?? false ? 'Tidak ada pelanggan di arsip.' : 'Belum ada pelanggan.' }}</p>
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
                            <button type="button" class="btn-icon h-9 w-9 text-slate-500 hover:text-indigo-600" onclick="RepairStation.openCustomerDetail({{ $customer->id }})" aria-label="Lihat detail">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            @if (($archived ?? false) && auth()->user()->isAdmin())
                                <button type="button" class="btn-icon h-9 w-9 text-slate-500 hover:text-emerald-600" onclick="RepairStation.restoreCustomer({{ $customer->id }})" aria-label="Pulihkan" title="Pulihkan">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                                <button type="button" class="btn-icon h-9 w-9 text-slate-500 hover:text-rose-600" onclick="RepairStation.destroyCustomerPermanent({{ $customer->id }})" aria-label="Hapus permanen" title="Hapus permanen">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            @elseif (auth()->user()->isAdmin())
                                <button type="button" class="btn-icon h-9 w-9 text-slate-500 hover:text-indigo-600" onclick="RepairStation.openCustomerForm({{ $customer->id }})" aria-label="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="btn-icon h-9 w-9 text-slate-500 hover:text-rose-600" onclick="RepairStation.deleteCustomer({{ $customer->id }})" aria-label="Arsipkan" title="Arsipkan">
                                    <i class="fa-solid fa-box-archive"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
