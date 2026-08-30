import { ajax, debounce } from '../lib/ajax';
import { openModal, closeModal, toast, swalConfirm, showErrors, clearErrors } from './common';

const form = document.getElementById('customer-form');

function resetForm() {
    clearErrors('customer-errors');
    document.getElementById('customer-id').value = '';
    document.getElementById('customer-nama').value = '';
    document.getElementById('customer-phone').value = '';
    document.getElementById('customer-alamat').value = '';
    document.getElementById('customer-submit-btn').textContent = 'Simpan';
    document.querySelector('#modal-customer-form h3').textContent = 'Form Pelanggan';
}

window.RepairStation.openCustomerForm = (customer = null) => {
    resetForm();

    if (customer) {
        document.getElementById('customer-id').value = customer.id;
        document.getElementById('customer-nama').value = customer.nama;
        document.getElementById('customer-phone').value = customer.no_hp;
        document.getElementById('customer-alamat').value = customer.alamat ?? '';
        document.getElementById('customer-submit-btn').textContent = 'Simpan Perubahan';
        document.querySelector('#modal-customer-form h3').textContent = 'Edit Pelanggan';
    }

    openModal('modal-customer-form');
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
        setTimeout(() => window.location.reload(), 500);
    } catch (err) {
        showErrors('customer-errors', err.errors ?? { error: [err.message] });
    }
});

window.RepairStation.deleteCustomer = async (id) => {
    const { isConfirmed } = await swalConfirm('Hapus pelanggan ini?', 'Data perangkat terkait juga ikut terhapus.', 'Ya, hapus', 'error');
    if (!isConfirmed) return;

    await ajax.delete(`/customers/${id}`);
    toast('Pelanggan dihapus.');
    setTimeout(() => window.location.reload(), 400);
};