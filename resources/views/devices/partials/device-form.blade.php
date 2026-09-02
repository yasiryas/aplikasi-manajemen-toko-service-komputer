<form id="device-form" data-url-create="{{ route('devices.store') }}" data-url-edit="/devices/">
    <input type="hidden" id="device-id">
    <input type="hidden" id="device-customer-id">
    <div class="space-y-4">
        <div>
            <label class="label">Jenis Perangkat</label>
            <div class="relative">
                <select id="device-jenis" class="input w-full pr-10 pr-12 appearance-none">
                    <option value="" disabled selected>Pilih jenis perangkat</option>
                    @foreach (\App\Enums\DeviceType::cases() as $jenis)
                        <option value="{{ $jenis->value }}">{{ $jenis->label() }}</option>
                    @endforeach
                </select>
                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500">
                    <i class="fa-solid fa-chevron-down text-sm"></i>
                </div>
            </div>
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