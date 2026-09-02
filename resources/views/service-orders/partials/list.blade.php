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
