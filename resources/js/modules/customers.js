import { ajax } from '../lib/ajax';
import {
    openModal,
    closeModal,
    toast,
    swalConfirm,
    showErrors,
    clearErrors,
    createListSwapper,
} from './common';

const form = document.getElementById('customer-form');

const refreshList = createListSwapper({
    input: 'customer-search',
    results: 'customer-results',
    pagination: 'customer-pagination',
    total: 'customer-total',
    url: '/customers/table',
    loading:
        '<p class="py-14 text-center text-sm text-slate-400">Mencari&hellip;</p>',
});

function resetForm() {
    clearErrors('customer-errors');
    document.getElementById('customer-id').value = '';
    document.getElementById('customer-nama').value = '';
    document.getElementById('customer-phone').value = '';
    document.getElementById('customer-alamat').value = '';
    document.getElementById('customer-submit-btn').textContent = 'Simpan';
    document.querySelector('#modal-customer-form h3').textContent =
        'Form Pelanggan';
}

window.RepairStation.openCustomerForm = async (customer = null) => {
    resetForm();
    openModal('modal-customer-form');

    if (!customer) return;

    try {
        const data =
            typeof customer === 'number'
                ? (await ajax.get(`/customers/${customer}/detail`)).customer
                : customer;

        document.getElementById('customer-id').value = data.id;
        document.getElementById('customer-nama').value = data.nama ?? '';
        document.getElementById('customer-phone').value = data.no_hp ?? '';
        document.getElementById('customer-alamat').value = data.alamat ?? '';
        document.getElementById('customer-submit-btn').textContent =
            'Simpan Perubahan';
        document.querySelector('#modal-customer-form h3').textContent =
            'Edit Pelanggan';
    } catch {
        showErrors('customer-errors', {
            error: ['Gagal memuat data pelanggan. Silakan coba lagi.'],
        });
    }
};

form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors('customer-errors');

    const id = document.getElementById('customer-id').value;
    const payload = {
        nama: document.getElementById('customer-nama').value,
        no_hp: document.getElementById('customer-phone').value,
        alamat: document.getElementById('customer-alamat').value,
    };

    try {
        if (id) {
            await ajax.put(`/customers/${id}`, payload);
            toast('Pelanggan diperbarui.');
        } else {
            await ajax.post('/customers', payload);
            toast('Pelanggan berhasil ditambahkan.');
        }

        closeModal('modal-customer-form');
        refreshList();
    } catch (err) {
        showErrors('customer-errors', err.errors ?? { error: [err.message] });
    }
});

window.RepairStation.deleteCustomer = async (id) => {
    const { isConfirmed } = await swalConfirm(
        'Arsipkan pelanggan ini?',
        'Pelanggan akan dipindah ke arsip. Data perangkat tetap tersimpan.',
        'Ya, arsipkan',
        'error',
    );
    if (!isConfirmed) return;

    await ajax.delete(`/customers/${id}`);
    toast('Pelanggan diarsipkan.');
    refreshList();
};

const escapeHtml = (value = '') =>
    String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;');

function renderDetailBody(customer) {
    const { devices = [] } = customer;
    let devicesHtml = '';

    if (devices.length === 0) {
        devicesHtml =
            '<p class="py-6 text-center text-sm text-slate-400">Belum ada perangkat.</p>';
    } else {
        devicesHtml = devices
            .map((device) => {
                const orders = (device.service_orders ?? [])
                    .map(
                        (order) => `
                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                        <span class="font-mono text-xs font-semibold text-indigo-600">${escapeHtml(order.no_tiket)}</span>
                        <span class="badge">${escapeHtml(order.status)}</span>
                    </li>`,
                    )
                    .join('');

                const orderBlock = orders.length
                    ? `<ul class="mt-3 space-y-1.5">${orders}</ul>`
                    : '<p class="mt-3 rounded-lg bg-slate-50 p-2 text-xs text-slate-400">Belum ada tiket service.</p>';

                return `
                    <div class="card p-4">
                        <p class="font-medium text-slate-900">${escapeHtml(device.merk)} ${escapeHtml(device.model ?? '')}</p>
                        <p class="text-xs text-slate-500">${escapeHtml(device.jenis)}</p>
                        <p class="mt-2 text-sm text-slate-600"><span class="font-semibold">Keluhan:</span> ${escapeHtml(device.keluhan)}</p>
                        ${orderBlock}
                    </div>`;
            })
            .join('');
    }

    return `
        <div class="mb-4 flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700">
                ${escapeHtml(customer.nama[0].toUpperCase())}
            </div>
            <div>
                <p class="font-semibold text-slate-900">${escapeHtml(customer.nama)}</p>
                <p class="font-mono text-xs text-slate-500">${escapeHtml(customer.no_hp)}</p>
                ${customer.alamat ? `<p class="text-xs text-slate-500">${escapeHtml(customer.alamat)}</p>` : ''}
            </div>
        </div>
        <div class="grid gap-3 md:grid-cols-2">${devicesHtml}</div>`;
}

window.RepairStation.openCustomerDetail = async (id) => {
    openModal('modal-customer-detail');
    document.getElementById('customer-detail-body').innerHTML =
        '<p class="py-6 text-center text-sm text-slate-400">Memuat&hellip;</p>';

    try {
        const { customer } = await ajax.get(`/customers/${id}/detail`);
        document.getElementById('customer-detail-body').innerHTML =
            renderDetailBody(customer);
        toast('Detail pelanggan berhasil dimuat.');
    } catch {
        document.getElementById('customer-detail-body').innerHTML =
            '<p class="py-6 text-center text-sm text-rose-500">Gagal memuat detail.</p>';
        toast('Gagal memuat detail pelanggan.', 'error');
    }
};

window.RepairStation.restoreCustomer = async (id) => {
    const { isConfirmed } = await swalConfirm(
        'Pulihkan pelanggan ini?',
        'Pelanggan akan kembali ke daftar aktif.',
        'Ya, pulihkan',
        'success',
    );
    if (!isConfirmed) return;

    await ajax.post(`/customers/${id}/restore`);
    toast('Pelanggan dipulihkan.');
    refreshList();
};

window.RepairStation.destroyCustomerPermanent = async (id) => {
    const { isConfirmed } = await swalConfirm(
        'Hapus permanen?',
        'Seluruh data pelanggan, perangkat, dan tiket terkait akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.',
        'Ya, hapus permanen',
        'error',
    );
    if (!isConfirmed) return;

    await ajax.delete(`/customers/${id}/permanent`);
    toast('Pelanggan dihapus permanen.');
    refreshList();
};

window.RepairStation.openImportModal = () => {
    const formEl = document.getElementById('customer-import-form');
    formEl?.reset();
    clearErrors('customer-import-errors');
    openModal('modal-customer-import');
};

const importForm = document.getElementById('customer-import-form');
importForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors('customer-import-errors');

    const file = document.getElementById('customer-import-file').files[0];
    if (!file) return;

    const data = new FormData();
    data.append('file', file);

    try {
        const { message } = await ajax.postForm('/customers/import', data);
        closeModal('modal-customer-import');
        toast(message);
        refreshList();
    } catch (err) {
        showErrors(
            'customer-import-errors',
            err.errors ?? { error: [err.message] },
        );
    }
});
