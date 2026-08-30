<form id="device-form" data-url-create="{{ route('devices.store') }}" data-url-edit="/devices/">
    <input type="hidden" id="device-id">
    <input type="hidden" id="device-customer-id">
    <div class="space-y-4">
        <div>
            <label class="label">Jenis Perangkat</label>
            <select id="device-jenis" class="input">
                @foreach (\App\Enums\DeviceType::cases() as $jenis)
                    <option value="{{ $jenis->value }}">{{ $jenis->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label" for="device-merk">Merk</label>
                <input type="text" id="device-merk" class="input" placeholder="ASUS, Lenovo&hellip;">
            </div>
            <div>
                <label class="label" for="device-model">Model</label>
                <input type="text" id="device-model" class="input" placeholder="Opsional">
            </div>
        </div>
        <div>
            <label class="label" for="device-keluhan">Keluhan</label>
            <textarea id="device-keluhan" rows="3" class="input" placeholder="Deskripsi kerusakan"></textarea>
        </div>
        <div id="device-errors" class="hidden rounded-lg bg-rose-50 p-3 text-sm text-rose-700"></div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="btn-secondary" data-close-modal>Batal</button>
            <button type="submit" id="device-submit-btn" class="btn-primary">Simpan</button>
        </div>
    </div>
</form>