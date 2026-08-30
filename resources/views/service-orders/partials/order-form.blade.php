<form id="order-form" data-url-create="{{ route('service-orders.store') }}" data-url-edit="/service-orders/" class="space-y-4">
    <input type="hidden" id="order-id" value="">

    <div>
        <label class="label" for="order-customer-search">Pelanggan</label>
        <input type="text" id="order-customer-search" class="input" placeholder="Cari nama / no. HP pelanggan&hellip;" autocomplete="off">
        <input type="hidden" id="order-customer-id">
        <ul id="order-customer-results" class="mt-1 hidden divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white text-sm shadow-sm"></ul>
    </div>

    <div id="order-device-container" class="hidden">
        <div class="flex items-center justify-between">
            <label class="label mb-0" for="order-device-id">Perangkat</label>
            <button type="button" id="order-add-device-btn" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">+ Perangkat Baru</button>
        </div>
        <select id="order-device-id" class="input mt-1">
            <option value="">Pilih perangkat&hellip;</option>
        </select>
    </div>

    <div id="order-new-device" class="hidden space-y-3 rounded-lg border border-dashed border-indigo-300 bg-indigo-50/50 p-3">
        <input type="hidden" id="order-new-device-customer">
        <div>
            <label class="label" for="order-device-jenis">Jenis Perangkat</label>
            <select id="order-device-jenis" class="input">
                @foreach (\App\Enums\DeviceType::cases() as $jenis)
                    <option value="{{ $jenis->value }}">{{ $jenis->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label" for="order-device-merk">Merk</label>
                <input type="text" id="order-device-merk" class="input" placeholder="ASUS, Lenovo&hellip;">
            </div>
            <div>
                <label class="label" for="order-device-model">Model</label>
                <input type="text" id="order-device-model" class="input" placeholder="Opsional">
            </div>
        </div>
        <div>
            <label class="label" for="order-device-keluhan">Keluhan</label>
            <textarea id="order-device-keluhan" rows="2" class="input" placeholder="Deskripsi kerusakan&hellip;"></textarea>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="label" for="order-status">Status</label>
            <select id="order-status" class="input">
                @foreach (\App\Enums\ServiceOrderStatus::cases() as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label" for="order-tech">Teknisi</label>
            <select id="order-tech" class="input">
                <option value="">Belum ditugaskan</option>
                @foreach ($technicians as $technician)
                    <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="label" for="order-estimate">Estimasi Biaya (Rp)</label>
        <input type="number" id="order-estimate" min="0" step="1000" class="input" placeholder="0">
    </div>

    <div id="order-errors" class="hidden rounded-lg bg-rose-50 p-3 text-sm text-rose-700"></div>

    <div class="flex justify-end gap-2 pt-2">
        <button type="button" class="btn-secondary" data-close-modal>Batal</button>
        <button type="submit" id="order-submit-btn" class="btn-primary">Buat Tiket</button>
    </div>
</form>