import { ajax } from '../lib/ajax';
import Swal from 'sweetalert2';
import { openModal, closeModal, toast, showErrors, clearErrors, fillSelect } from './common';

const itemsBox = document.getElementById('invoice-items');
const totalEl = document.getElementById('invoice-total');

function formatRupiah(value) {
    return 'Rp '.concat(Number(value).toLocaleString('id-ID').replace(/,/g, '.'));
}

function computeTotal() {
    if (!itemsBox || !totalEl) return;

    const total = [...itemsBox.children].reduce((sum, row) => {
        const qty = Number(row.querySelector('[data-item-qty]')?.value ?? 0);
        const price = Number(row.querySelector('[data-item-price]')?.value ?? 0);

        return sum + qty * price;
    }, 0);

    totalEl.textContent = formatRupiah(total);
}

function addItemRow() {
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 rounded-lg border border-slate-200 bg-slate-50/50 p-2';
    row.innerHTML = `
        <input type="text" data-item-name class="input col-span-12 text-sm" placeholder="Nama item (mis. Jasa ganti LCD)">
        <select data-item-type class="input col-span-3 text-sm"><option value="jasa">Jasa</option><option value="sparepart">Sparepart</option></select>
        <input type="number" data-item-qty min="1" value="1" class="input col-span-2 text-sm" placeholder="Qty">
        <input type="number" data-item-price min="0" step="1000" class="input col-span-6 text-sm" placeholder="Harga (Rp)">
        <button type="button" data-item-remove class="col-span-1 flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600" aria-label="Hapus item">
            <i class="fa-solid fa-xmark"></i>
        </button>`;

    itemsBox.appendChild(row);
    computeTotal();
}

document.getElementById('invoice-add-item')?.addEventListener('click', addItemRow);

itemsBox?.addEventListener('input', computeTotal);
itemsBox?.addEventListener('click', (e) => {
    const removeBtn = e.target.closest('[data-item-remove]');

    if (!removeBtn) return;

    if (itemsBox.children.length === 1) {
        toast('Minimal satu item invoice.', 'error');
        return;
    }

    removeBtn.closest('div.grid').remove();
    computeTotal();
});

async function populateOrders() {
    const select = document.getElementById('invoice-order');

    if (!select) return;

    select.innerHTML = '<option value="">Memuat…</option>';

    try {
        const { orders } = await ajax.get('/api/invoices/ready-orders');
        fillSelect(select, orders.map((o) => ({ value: o.id, label: `${o.no_tiket} — ${o.customer} (${o.perangkat})` })));

        if (window.currentOrderId) {
            select.value = window.currentOrderId;
        }
    } catch {
        fillSelect(select, [], 'Gagal memuat tiket');
    }
}

const invoiceForm = document.getElementById('invoice-form');

invoiceForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors('invoice-errors');

    const rows = [...itemsBox.children];

    const items = rows.map((row) => ({
        nama_item: row.querySelector('[data-item-name]').value,
        tipe: row.querySelector('[data-item-type]').value,
        qty: Number(row.querySelector('[data-item-qty]').value),
        harga: Number(row.querySelector('[data-item-price]').value),
    }));

    const payload = {
        service_order_id: Number(document.getElementById('invoice-order').value),
        status_bayar: document.getElementById('invoice-status').value,
        metode_bayar: document.getElementById('invoice-method').value || null,
        items,
    };

    try {
        await ajax.post('/invoices', payload);
        closeModal('modal-invoice-form');
        toast('Invoice berhasil dibuat.');
        setTimeout(() => window.location.reload(), 600);
    } catch (err) {
        showErrors('invoice-errors', err.errors ?? { error: [err.message] });
    }
});

window.RepairStation.markPaid = async (invoiceId, fromDetail = false) => {
    const { value: metode } = await Swal.fire({
        title: 'Tandai Lunas',
        text: 'Pilih metode pembayaran',
        icon: 'question',
        input: 'select',
        inputOptions: {
            tunai: 'Tunai',
            transfer: 'Transfer',
            lainnya: 'Lainnya',
        },
        inputPlaceholder: 'Pilih metode…',
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#4f46e5',
        reverseButtons: true,
        customClass: { popup: 'rounded-xl' },
    });

    if (!metode) return;

    try {
        await ajax.patch(`/invoices/${invoiceId}/payment`, { metode_bayar: metode });
        toast('Invoice ditandai lunas.');
        setTimeout(() => window.location.reload(), 500);
    } catch (err) {
        toast(err.message ?? 'Gagal menandai lunas.', 'error');
    }
};

// Persiapan saat modal invoice dibuka
window.RepairStation.openInvoiceModal = () => {
    const modal = document.getElementById('modal-invoice-form');

    if (!modal) return;

    modal.dataset.initialized = Date.now();
    itemsBox.innerHTML = '';
    window.currentOrderId = window.currentOrderId ?? null;
    openModal('modal-invoice-form');
    populateOrders();
    addItemRow();
};