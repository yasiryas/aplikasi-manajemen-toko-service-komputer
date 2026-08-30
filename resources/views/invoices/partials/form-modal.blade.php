@if (auth()->user()->isAdmin())
    <x-modal id="modal-invoice-form" title="Buat Invoice" maxWidth="md:max-w-2xl">
        <form id="invoice-form" data-url-create="{{ route('invoices.store') }}" class="space-y-4">
            <div>
                <label class="label" for="invoice-order">Tiket Selesai</label>
                <select id="invoice-order" class="input">
                    <option value="">— Pilih tiket yang sudah selesai —</option>
                </select>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="label mb-0">Item Jasa / Sparepart</label>
                    <button type="button" id="invoice-add-item" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">+ Tambah Item</button>
                </div>

                <div id="invoice-items" class="space-y-2">
                    <div class="grid grid-cols-12 gap-2 rounded-lg border border-slate-200 bg-slate-50/50 p-2">
                        <input type="text" data-item-name class="input col-span-12 text-sm" placeholder="Nama item (mis. Jasa ganti LCD)">
                        <select data-item-type class="input col-span-3 text-sm">
                            <option value="jasa">Jasa</option>
                            <option value="sparepart">Sparepart</option>
                        </select>
                        <input type="number" data-item-qty min="1" value="1" class="input col-span-2 text-sm" placeholder="Qty">
                        <input type="number" data-item-price min="0" step="1000" class="input col-span-6 text-sm" placeholder="Harga (Rp)">
                        <button type="button" data-item-remove class="btn-icon col-span-1 h-9 w-9 text-slate-400 hover:text-rose-600" aria-label="Hapus item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between rounded-lg bg-slate-50 px-4 py-3">
                <span class="text-sm font-medium text-slate-600">Total</span>
                <span id="invoice-total" class="font-heading text-xl font-bold text-indigo-600">Rp 0</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label" for="invoice-status">Status Pembayaran</label>
                    <select id="invoice-status" class="input">
                        <option value="belum_lunas">Belum Lunas</option>
                        <option value="lunas">Lunas</option>
                    </select>
                </div>
                <div>
                    <label class="label" for="invoice-method">Metode</label>
                    <select id="invoice-method" class="input">
                        <option value="">— Pilih —</option>
                        <option value="tunai">Tunai</option>
                        <option value="transfer">Transfer</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <div id="invoice-errors" class="hidden rounded-lg bg-rose-50 p-3 text-sm text-rose-700"></div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="btn-secondary" data-close-modal>Batal</button>
                <button type="submit" class="btn-primary">Simpan Invoice</button>
            </div>
        </form>
    </x-modal>
@endif