<div class="card mt-5 overflow-hidden">
    @if ($invoices->isEmpty())
        <p class="py-14 text-center text-sm text-slate-400">Belum ada invoice.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Invoice</th>
                        <th class="px-4 py-3 font-semibold">Tiket</th>
                        <th class="px-4 py-3 font-semibold">Pelanggan</th>
                        <th class="px-4 py-3 font-semibold">Total</th>
                        <th class="px-4 py-3 font-semibold">Pembayaran</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($invoices as $invoice)
                        <tr data-invoice-id="{{ $invoice->id }}" class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-semibold text-slate-900">#{{ str_pad((string) $invoice->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-3 font-mono text-xs font-semibold text-indigo-600">{{ $invoice->serviceOrder->no_tiket }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $invoice->serviceOrder->device->customer->nama }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ rupiah($invoice->total_biaya) }}</td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $invoice->isLunas() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $invoice->status_bayar->label() }}</span>
                                <span class="ml-1 text-xs text-slate-400">{{ $invoice->metode_bayar?->label() }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="btn-icon h-9 w-9 text-slate-500 hover:text-indigo-600" aria-label="Cetak">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    @if (auth()->user()->isAdmin() && ! $invoice->isLunas())
                                        <button type="button" class="btn-secondary h-9 px-3" onclick="RepairStation.markPaid({{ $invoice->id }})"><i class="fa-solid fa-circle-check mr-1"></i>Tandai Lunas</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="mt-5">
    {{ $invoices->links() }}
</div>
