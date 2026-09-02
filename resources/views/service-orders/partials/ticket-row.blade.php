<tr data-order-id="{{ $order->id }}" class="hover:bg-slate-50/70">
    <td class="px-4 py-3">
        <span class="font-mono text-xs font-semibold text-indigo-600">{{ $order->no_tiket }}</span>
    </td>
    <td class="px-4 py-3">
        <p class="font-medium text-slate-900">{{ $order->device->customer->nama }}</p>
        <p class="text-xs text-slate-500">{{ $order->device->customer->no_hp }}</p>
    </td>
    <td class="px-4 py-3 text-slate-600">{{ $order->device->merk }} {{ $order->device->model }}</td>
    <td class="px-4 py-3">
        @include('partials.status-badge', ['status' => $order->status])
    </td>
    <td class="px-4 py-3 text-slate-600">{{ $order->estimasi_biaya ? rupiah($order->estimasi_biaya) : '—' }}</td>
    <td class="px-4 py-3 text-slate-600">{{ $order->teknisi?->name ?? '—' }}</td>
    <td class="px-4 py-3">
        <div class="flex items-center justify-end gap-1.5">
            <button type="button" class="btn-icon h-9 w-9 text-slate-500 hover:text-indigo-600" onclick="RepairStation.openOrderDetail({{ $order->id }})" title="Lihat detail" aria-label="Lihat detail">
                <i class="fa-solid fa-eye"></i>
            </button>
            @if ($order->status->value !== 'diambil')
<div class="relative w-full">
                <select class="w-full h-9 rounded-lg border border-slate-300 bg-white px-2 text-xs text-slate-600 focus:border-indigo-500 focus:outline-none appearance-none" data-status-select aria-label="Ubah status">
                    <option value="" disabled selected>Pilih status</option>
                    @foreach (\App\Enums\ServiceOrderStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ $order->status === $status ? 'selected' : '' }}>{{ $status->label() }}</option>
                    @endforeach
                </select>
                <div class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-500">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>
            </div>
            @endif
            @if (auth()->user()->isAdmin())
                <button type="button" class="btn-icon h-9 w-9 text-slate-500 hover:text-rose-600" onclick="RepairStation.deleteOrder({{ $order->id }})" title="Hapus" aria-label="Hapus">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            @endif
        </div>
    </td>
</tr>