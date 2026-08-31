<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Invoice {{ $invoice->serviceOrder->no_tiket }} - {{ setting('nama_toko', 'Service Computer') }}</title>
        <link rel="icon" href="{{ logo_url() ?? asset('icons/icon-192.png') }}">
        @fonts
        @vite(['resources/css/app.css'])
    </head>
    <body class="bg-slate-100">
        <div class="mx-auto my-8 w-full max-w-md rounded-2xl bg-white p-8 shadow-lg print:my-0 print:max-w-none print:rounded-none print:shadow-none">
            <div class="flex items-start justify-between border-b border-dashed border-slate-300 pb-4">
                <div class="flex items-start gap-3">
                    @php($logo = logo_url())
                    @if ($logo)
                        <img src="{{ $logo }}" alt="Logo" class="h-12 w-12 rounded-lg object-contain ring-1 ring-slate-100">
                    @endif
                    <div>
                        <p class="font-heading text-xl font-bold text-indigo-600">{{ setting('nama_toko', 'Service Computer') }}</p>
                        <p class="text-xs text-slate-500">{{ setting('tagline_toko', 'Service Komputer') }}</p>
                        @if (setting('telepon_toko'))
                            <p class="text-xs text-slate-500"><i class="fa-solid fa-phone mr-1"></i>{{ setting('telepon_toko') }}</p>
                        @endif
                        @if (setting('alamat_toko'))
                            <p class="mt-1 whitespace-pre-line text-xs text-slate-400">{{ setting('alamat_toko') }}</p>
                        @endif
                    </div>
                </div>
                <p class="text-right">
                    <span class="font-mono text-xs font-semibold text-slate-400">#{{ str_pad((string) $invoice->id, 4, '0', STR_PAD_LEFT) }}</span><br>
                    <span class="text-xs text-slate-400">Tanggal: {{ tanggal($invoice->created_at, 'd F Y') }}</span>
                </p>
            </div>

            <div class="mt-4 space-y-1 text-sm text-slate-600">
                <p><span class="font-semibold">No. Tiket:</span> <span class="font-mono text-indigo-600">{{ $invoice->serviceOrder->no_tiket }}</span></p>
                <p><span class="font-semibold">Tanggal Masuk:</span> {{ tanggal($invoice->serviceOrder->tanggal_masuk, 'd/m/Y') }}</p>
                <p><span class="font-semibold">Pelanggan:</span> {{ $invoice->serviceOrder->device->customer->nama }}</p>
                <p><span class="font-semibold">Perangkat:</span> {{ $invoice->serviceOrder->device->merk }} {{ $invoice->serviceOrder->device->model }} ({{ $invoice->serviceOrder->device->jenis->label() }})</p>
            </div>

            <table class="mt-5 w-full text-sm">
                <thead>
                    <tr class="border-b text-xs uppercase tracking-wide text-slate-400">
                        <th class="pb-2 text-left font-semibold">Item</th>
                        <th class="pb-2 text-center font-semibold">Qty</th>
                        <th class="pb-2 text-right font-semibold">Harga</th>
                        <th class="pb-2 text-right font-semibold">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td class="py-2 text-slate-700">
                                {{ $item->nama_item }}
                                <span class="ml-1 text-xs text-slate-400">{{ $item->tipe === 'jasa' ? 'jasa' : 'sparepart' }}</span>
                            </td>
                            <td class="py-2 text-center text-slate-600">{{ $item->qty }}</td>
                            <td class="py-2 text-right text-slate-600">{{ rupiah($item->harga) }}</td>
                            <td class="py-2 text-right font-medium text-slate-900">{{ rupiah($item->subtotal()) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-900/10">
                        <td colspan="3" class="py-3 text-right font-semibold text-slate-900">TOTAL</td>
                        <td class="py-3 text-right font-heading text-lg font-bold text-indigo-600">{{ rupiah($invoice->total_biaya) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right text-sm">
                            <span class="badge {{ $invoice->isLunas() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $invoice->status_bayar->label() }}</span>
                            @if ($invoice->metode_bayar)
                                <span class="ml-1 text-xs text-slate-400">{{ $invoice->metode_bayar->label() }}</span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>

            <p class="mt-8 text-center text-xs text-slate-400">{{ setting('footer_invoice', 'Terima kasih telah mempercayakan perbaikan kepada kami.') }}</p>
        </div>

        <script>
            window.onload = () => window.print();
        </script>
    </body>
</html>