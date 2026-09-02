@forelse ($devices as $device)
    <div class="card p-4">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <p class="truncate font-medium text-slate-900">{{ $device->merk }} {{ $device->model }}</p>
                @if ($device->customer && ! $device->customer->trashed())
                    <a href="{{ route('customers.show', $device->customer) }}" class="text-xs text-indigo-600 hover:text-indigo-700">{{ $device->customer->nama }}</a>
                @elseif ($device->customer)
                    <span class="text-xs text-slate-400">Pelanggan terarsip</span>
                @endif
            </div>
            <span class="badge bg-slate-100 text-slate-600">{{ $device->jenis->label() }}</span>
        </div>
        <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $device->keluhan }}</p>
        <div class="mt-3 flex justify-end gap-1.5 border-t border-slate-100 pt-2.5">
        @if (auth()->user()->isAdmin())
            <button type="button" class="btn-icon h-9 w-9 text-slate-500 hover:text-indigo-600" onclick="RepairStation.openDeviceForm({{ $device->customer_id }}, {{ $device->id }})" aria-label="Edit">
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
