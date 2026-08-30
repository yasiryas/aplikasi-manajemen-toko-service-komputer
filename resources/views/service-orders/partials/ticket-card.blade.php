<div class="card ticket-stub mx-2 cursor-pointer p-4 transition-shadow hover:shadow-md" onclick="RepairStation.openOrderDetail({{ $order->id }})">
    <div class="flex items-start justify-between gap-2">
        <span class="font-mono text-xs font-semibold text-indigo-600">{{ $order->no_tiket }}</span>
        @include('partials.status-badge', ['status' => $order->status])
    </div>

    <div class="mt-2">
        <p class="font-medium text-slate-900">{{ $order->device->customer->nama }}</p>
        <p class="text-xs text-slate-500">{{ $order->device->merk }} {{ $order->device->model }} · {{ $order->tanggal_masuk->format('d/m/Y') }}</p>
    </div>

    <div class="mt-3 flex items-center justify-between border-t border-dashed border-slate-200 pt-2.5">
        <div class="text-xs text-slate-500">
            <span>{{ $order->teknisi?->name ?? 'Belum ada teknisi' }}</span>
        </div>
        <div class="flex items-center gap-1.5" @click.stop>
            @if ($order->status->value !== 'diambil')
                <select class="h-9 rounded-lg border border-slate-300 bg-white px-2 text-xs text-slate-600 focus:border-indigo-500 focus:outline-none" data-status-select aria-label="Ubah status">
                    @foreach (\App\Enums\ServiceOrderStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ $order->status === $status ? 'selected' : '' }}>{{ $status->label() }}</option>
                    @endforeach
                </select>
            @endif
            @if (auth()->user()->isAdmin())
                <button type="button" class="btn-icon h-9 w-9 !h-9 text-slate-500 hover:text-rose-600" onclick="RepairStation.deleteOrder({{ $order->id }})" aria-label="Hapus">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            @endif
        </div>
    </div>
</div>