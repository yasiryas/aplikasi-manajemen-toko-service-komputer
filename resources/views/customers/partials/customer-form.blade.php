<form id="customer-form" data-url-create="{{ route('customers.store') }}" data-url-edit="/customers/">
    <input type="hidden" id="customer-id">
    <div class="space-y-4">
        <div>
            <label class="label" for="customer-nama">Nama</label>
            <input type="text" id="customer-nama" class="input" placeholder="Nama lengkap pelanggan">
        </div>
        <div>
            <label class="label" for="customer-phone">No. HP / WhatsApp</label>
            <input type="text" id="customer-phone" class="input" placeholder="08xxxxxxxxxx">
        </div>
        <div>
            <label class="label" for="customer-alamat">Alamat</label>
            <textarea id="customer-alamat" rows="3" class="input" placeholder="Opsional"></textarea>
        </div>
        <div id="customer-errors" class="hidden rounded-lg bg-rose-50 p-3 text-sm text-rose-700"></div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="btn-secondary" data-close-modal>Batal</button>
            <button type="submit" id="customer-submit-btn" class="btn-primary">Simpan</button>
        </div>
    </div>
</form>