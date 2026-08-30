import { ajax, debounce } from '../lib/ajax';
import { openModal, closeModal, toast, swalConfirm, showErrors, clearErrors } from './common';

const form = document.getElementById('device-form');

function resetForm(customerId = '') {
    clearErrors('device-errors');
    document.getElementById('device-id').value = '';
    document.getElementById('device-customer-id').value = customerId ?? '';
    document.getElementById('device-merk').value = '';
    document.getElementById('device-model').value = '';
    document.getElementById('device-keluhan').value = '';
    document.getElementById('device-submit-btn').textContent = 'Simpan';
    document.querySelector('#modal-device-form h3').textContent = 'Form Perangkat';
}

window.RepairStation.openDeviceForm = (customerId, device = null) => {
    resetForm(customerId);

    const picker = document.getElementById('device-owner-picker');

    if (picker) {
        const search = document.getElementById('device-owner-search');
        search.value = '';
        search.readOnly = Boolean(device);
        search.placeholder = device ? 'Pelanggan dari perangkat ini' : 'Cari nama / no. HP pelanggan…';
        document.getElementById('device-owner-results').classList.add('hidden');
    }

    if (device) {
        document.getElementById('device-customer-id').value = device.customer_id ?? customerId;
        document.getElementById('device-id').value = device.id;
        document.getElementById('device-jenis').value = device.jenis;
        document.getElementById('device-merk').value = device.merk;
        document.getElementById('device-model').value = device.model ?? '';
        document.getElementById('device-keluhan').value = device.keluhan;
        document.getElementById('device-submit-btn').textContent = 'Simpan Perubahan';
        document.querySelector('#modal-device-form h3').textContent = 'Edit Perangkat';
    }

    openModal('modal-device-form');
};

// Owner picker (halaman daftar perangkat)
const ownerSearch = document.getElementById('device-owner-search');
const ownerResults = document.getElementById('device-owner-results');

ownerSearch?.addEventListener('input', debounce(async () => {
    const q = ownerSearch.value;

    if (q.trim().length < 2 || ownerSearch.readOnly) {
        ownerResults.classList.add('hidden');
        return;
    }

    const { customers } = await ajax.get(`/api/customers/search?q=${encodeURIComponent(q)}`);
    ownerResults.innerHTML = '';

    if (!customers.length) {
        ownerResults.innerHTML = '<li class="px-3 py-2 text-slate-400">Tidak ditemukan.</li>';
    } else {
        customers.forEach((c) => {
            const li = document.createElement('li');
            li.className = 'flex cursor-pointer items-center justify-between px-3 py-2 hover:bg-slate-50';
            li.dataset.customerId = c.id;
            li.dataset.customerName = c.nama;
            li.innerHTML = `<span class="font-medium text-slate-900">${c.nama}</span><span class="font-mono text-xs text-slate-400">${c.no_hp}</span>`;
            ownerResults.appendChild(li);
        });
    }
    ownerResults.classList.remove('hidden');
}, 300));

ownerResults?.addEventListener('click', (e) => {
    const li = e.target.closest('[data-customer-id]');
    if (!li) return;

    ownerSearch.value = li.dataset.customerName;
    document.getElementById('device-customer-id').value = li.dataset.customerId;
    ownerResults.classList.add('hidden');
});

form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors('device-errors');

    const id = document.getElementById('device-id').value;
    const payload = {
        jenis: document.getElementById('device-jenis').value,
        merk: document.getElementById('device-merk').value,
        model: document.getElementById('device-model').value,
        keluhan: document.getElementById('device-keluhan').value,
    };

    try {
        if (id) {
            await ajax.put(`/devices/${id}`, payload);
            toast('Perangkat diperbarui.');
        } else {
            payload.customer_id = Number(document.getElementById('device-customer-id').value);
            await ajax.post('/devices', payload);
            toast('Perangkat ditambahkan.');
        }

        closeModal('modal-device-form');
        setTimeout(() => window.location.reload(), 500);
    } catch (err) {
        showErrors('device-errors', err.errors ?? { error: [err.message] });
    }
});

window.RepairStation.deleteDevice = async (id) => {
    const { isConfirmed } = await swalConfirm('Hapus perangkat ini?', 'Tiket terkait ikut terhapus.', 'Ya, hapus', 'error');
    if (!isConfirmed) return;

    await ajax.delete(`/devices/${id}`);
    toast('Perangkat dihapus.');
    setTimeout(() => window.location.reload(), 400);
};