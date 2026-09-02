import { ajax, debounce } from '../lib/ajax';
import { toast, confirmDialog } from '../lib/dialog';

// Modal helpers ------------------------------------------------------------

let lastFocused = null;

export function openModal(id) {
    const el = document.getElementById(id);

    if (!el) return;

    lastFocused = document.activeElement;
    el.classList.remove('hidden');
    el.classList.add('flex');
    el.removeAttribute('x-cloak');
    el.style.display = 'flex';
    el.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    const focusable = el.querySelector(
        'input:not([type="hidden"]), textarea, select, button:not([data-close-modal])',
    );
    setTimeout(() => focusable?.focus(), 0);
}

export function closeModal(id) {
    const el = document.getElementById(id);

    if (!el) return;

    el.classList.add('hidden');
    el.classList.remove('flex');
    el.style.display = '';
    el.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    lastFocused?.focus?.();
    lastFocused = null;
}

window.openModal = openModal;
window.closeModal = closeModal;

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;

    document.querySelectorAll('[class*="inset-0 z-50"]').forEach((el) => {
        if (!el.classList.contains('hidden')) closeModal(el.id);
    });
    document.body.style.overflow = '';
});

document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-close-modal]');

    if (!trigger) return;

    const target = trigger.closest('div[id][class*="inset-0 z-50"]');
    if (target) closeModal(target.id);
});

// Dialog helpers (Tailwind) --------------------------------------------------

export { toast };

export function swalConfirm(
    title,
    text,
    confirmLabel = 'Ya, lanjutkan',
    dangerType = 'warning',
) {
    return confirmDialog({
        title,
        text,
        confirmText: confirmLabel,
        danger: dangerType === 'error',
    }).then((confirmed) => ({ isConfirmed: confirmed }));
}

window.toast = toast;
window.swalConfirm = swalConfirm;

// Shared helpers ------------------------------------------------------------

export function showErrors(id, errors) {
    const box = document.getElementById(id);

    if (!box) return;

    const messages = Object.values(errors).flat();

    box.innerHTML = messages.map((m) => `<p>${m}</p>`).join('');
    box.classList.remove('hidden');
}

export function clearErrors(id) {
    document.getElementById(id)?.classList.add('hidden');
}

export function createListSwapper({
    input,
    results,
    pagination,
    total,
    url,
    loading,
}) {
    const inputEl = input ? document.getElementById(input) : null;
    const resultsEl = document.getElementById(results);

    const refresh = debounce(async () => {
        if (!resultsEl) return;

        const original = resultsEl.innerHTML;
        if (loading) resultsEl.innerHTML = loading;

        const query = new URLSearchParams();
        if (inputEl) query.set('q', inputEl.value.trim());
        const currentUrl = window.location;
        const status = currentUrl.searchParams?.get('status');
        if (status) query.set('status', status);

        const suffix = query.toString() ? `?${query}` : '';
        try {
            const data = await ajax.get(`${url}${suffix}`);
            if (resultsEl) resultsEl.innerHTML = data.html;
            if (pagination)
                document.getElementById(pagination).innerHTML = data.pagination;
            if (total && data.total !== undefined)
                document.getElementById(total).textContent = data.total;
        } catch {
            if (resultsEl) resultsEl.innerHTML = original;
        }
    }, 300);

    if (inputEl) {
        inputEl.addEventListener('input', refresh);

        const form = inputEl.closest('form');
        if (form) {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                refresh();
            });
        }
    }

    return refresh;
}

export function bindRealtimeSearch(options) {
    return createListSwapper(options);
}

export function fillSelect(select, options, placeholder) {
    select.innerHTML = '';

    if (placeholder) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;
        select.appendChild(option);
    }

    options.forEach((item) => {
        const option = document.createElement('option');
        option.value = item.value;
        option.textContent = item.label;
        select.appendChild(option);
    });
}

// Order detail modal --------------------------------------------------------

async function openOrderDetail(orderId) {
    window.currentOrderId = orderId;
    openModal('modal-order-detail');

    const body = document.getElementById('order-detail-body');
    body.innerHTML =
        '<p class="py-6 text-center text-sm text-slate-400">Memuat&hellip;</p>';

    try {
        const { order } = await ajax.get(`/service-orders/${orderId}`);
        body.innerHTML = renderOrderDetail(order);
    } catch {
        body.innerHTML =
            '<p class="py-6 text-center text-sm text-rose-600">Gagal memuat detail tiket.</p>';
    }
}

function badgeHtml(status, extraClass = '') {
    const classes = {
        antri: 'bg-amber-100 text-amber-700',
        dikerjakan: 'bg-indigo-100 text-indigo-700',
        menunggu_sparepart: 'bg-rose-100 text-rose-700',
        selesai: 'bg-emerald-100 text-emerald-700',
        diambil: 'bg-emerald-100 text-emerald-700',
    };
    const labels = {
        antri: 'Antri',
        dikerjakan: 'Dikerjakan',
        menunggu_sparepart: 'Menunggu Sparepart',
        selesai: 'Selesai',
        diambil: 'Diambil',
    };

    return `<span class="badge ${classes[status] ?? 'bg-slate-100 text-slate-700'} ${extraClass}">${labels[status] ?? status}</span>`;
}

function renderOrderDetail(order) {
    const triggerLocked = order.status === 'diambil';

    const statusOptions = [
        ['antri', 'Antri'],
        ['dikerjakan', 'Dikerjakan'],
        ['menunggu_sparepart', 'Menunggu Sparepart'],
        ['selesai', 'Selesai'],
        ['diambil', 'Diambil'],
    ]
        .map(
            ([value, label]) =>
                `<option value="${value}" ${order.status === value ? 'selected' : ''}>${label}</option>`,
        )
        .join('');

    const logs =
        (order.logs ?? [])
            .map(
                (log) => `<li class="relative pb-4 pl-5">
                <span class="absolute left-0 top-1.5 h-2.5 w-2.5 rounded-full bg-indigo-500"></span>
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span class="font-medium text-slate-700">${log.catatan ?? 'Status diperbarui'}</span>
                </div>
                <p class="text-xs text-slate-400">${log.changed_by?.name ?? 'Sistem'} · ${new Date(log.created_at).toLocaleString('id-ID')}</p>
            </li>`,
            )
            .join('') ||
        '<li class="text-sm text-slate-400">Belum ada catatan.</li>';

    const notifications =
        (order.notification_logs ?? [])
            .map(
                (
                    n,
                ) => `<p class="text-xs text-slate-500 flex items-center gap-1.5">
                <span class="${n.status === 'terkirim' ? 'text-emerald-500' : 'text-rose-500'}">●</span>
                ${n.channel} · ${n.status === 'terkirim' ? 'terkirim' : 'gagal'} · ${new Date(n.created_at).toLocaleString('id-ID')}
            </p>`,
            )
            .join('') ||
        '<p class="text-xs text-slate-400">Belum ada notifikasi terkirim.</p>';

    const invoice = order.invoice;
    const isAdmin = document.body.dataset.admin === '1';
    const isStaff = document.body.dataset.staff === '1';

    const invoiceBlock = invoice
        ? `<div class="flex items-center justify-between rounded-lg bg-slate-50 px-4 py-3">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Invoice #${String(invoice.id).padStart(4, '0')}</p>
                    <p class="text-xs text-slate-500">Total: Rp ${Number(invoice.total_biaya).toLocaleString('id-ID')}</p>
                </div>
                <span class="badge ${invoice.status_bayar === 'lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}">${invoice.status_bayar === 'lunas' ? 'Lunas' : 'Belum Lunas'}</span>
            </div>
            <div class="mt-2 flex gap-2">
                <a href="/invoices/${invoice.id}/print" target="_blank" class="btn-secondary flex-1 !py-2">Cetak Invoice</a>
                ${invoice.status_bayar !== 'lunas' && isAdmin ? `<button type="button" class="btn-primary flex-1 !py-2" onclick="RepairStation.markPaid(${invoice.id}, true)">Tandai Lunas</button>` : ''}
            </div>`
        : '';

    const invoiceAction =
        isAdmin && !invoice && order.status === 'selesai'
            ? `<button type="button" class="btn-primary mb-4" onclick="RepairStation.openInvoiceModal()">Buat Invoice</button>`
            : '';

    return `
        <div class="mb-4 flex items-center gap-3">
            <span class="font-mono text-sm font-semibold text-indigo-600">${order.no_tiket}</span>
            ${badgeHtml(order.status)}
        </div>

        <div class="grid gap-3 rounded-lg bg-slate-50 p-4 text-sm sm:grid-cols-2">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-400">Pelanggan</p>
                <p class="font-medium text-slate-900">${order.device.customer.nama}</p>
                <p class="text-xs text-slate-500">${order.device.customer.no_hp}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-400">Perangkat</p>
                <p class="font-medium text-slate-900">${order.device.merk} ${order.device.model ?? ''}</p>
                <p class="text-xs text-slate-500">${order.device.jenis} · ${order.device.keluhan}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-400">Teknisi</p>
                <p class="font-medium text-slate-900">${order.teknisi?.name ?? 'Belum ditugaskan'}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-400">Estimasi</p>
                <p class="font-medium text-slate-900">${order.estimasi_biaya ? 'Rp ' + Number(order.estimasi_biaya).toLocaleString('id-ID') : '—'}</p>
            </div>
        </div>

        ${
            isStaff
                ? `<div class="mt-5">
            <div class="flex items-center justify-between">
                <h3 class="font-heading text-sm font-bold text-slate-900">Ubah Status</h3>
                ${order.notification_logs?.length ? '' : ''}
            </div>
            <div class="mt-2 flex gap-2">
                <select id="detail-status-select" class="input flex-1" ${triggerLocked ? 'disabled' : ''}>${statusOptions}</select>
                <button type="button" class="btn-primary" data-detail-status-save ${triggerLocked ? 'disabled' : ''}>Simpan</button>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
                <button type="button" class="btn-secondary !py-2" onclick="RepairStation.reSendNotification(${order.id})">Kirim Ulang Notifikasi</button>
            </div>
        </div>`
                : ''
        }

        <div class="mt-5">
            <h3 class="mb-2 font-heading text-sm font-bold text-slate-900">Invoice</h3>
            ${invoiceBlock || `<p class="rounded-lg bg-slate-50 p-3 text-xs text-slate-400">Belum ada invoice.${order.status === 'selesai' ? ' Tiket sudah selesai, siap dibuatkan invoice.' : ''}</p>`}
        </div>

        ${invoiceAction}

        <div class="mt-5">
            <h3 class="mb-3 font-heading text-sm font-bold text-slate-900">Riwayat Status</h3>
            <ol class="border-l border-slate-200 pl-1">${logs}</ol>
        </div>

        <div class="mt-5">
            <h3 class="mb-2 font-heading text-sm font-bold text-slate-900">Log Notifikasi</h3>
            <div class="space-y-1">${notifications}</div>
        </div>
    `;
}

// Order form (create / edit) ------------------------------------------------

const orderForm = {
    async init() {
        this.root = document.getElementById('order-form');
        if (!this.root) return;

        this.bindEvents();
    },

    bindEvents() {
        this.root.addEventListener('submit', (e) => this.submit(e));

        const search = document.getElementById('order-customer-search');

        search.addEventListener(
            'input',
            debounce(() => this.searchCustomers(search.value), 350),
        );
        document
            .getElementById('order-customer-results')
            .addEventListener('click', (e) => {
                const li = e.target.closest('[data-customer-id]');
                if (li)
                    this.selectCustomer(
                        Number(li.dataset.customerId),
                        li.dataset.customerName,
                    );
            });

        document
            .getElementById('order-add-device-btn')
            ?.addEventListener('click', () => {
                document
                    .getElementById('order-new-device')
                    .classList.remove('hidden');
                document.getElementById('order-device-id').value = '';
            });
    },

    async searchCustomers(q) {
        const results = document.getElementById('order-customer-results');

        if (q.trim().length < 2) {
            results.classList.add('hidden');
            return;
        }

        const { customers } = await ajax.get(
            `/api/customers/search?q=${encodeURIComponent(q)}`,
        );
        results.innerHTML = '';

        if (!customers.length) {
            results.innerHTML =
                '<li class="px-3 py-2 text-slate-400">Tidak ditemukan.</li>';
        } else {
            customers.forEach((c) => {
                const li = document.createElement('li');
                li.className =
                    'flex cursor-pointer items-center justify-between px-3 py-2 hover:bg-slate-50';
                li.dataset.customerId = c.id;
                li.dataset.customerName = c.nama;
                li.innerHTML = `<span class="font-medium text-slate-900">${c.nama}</span><span class="font-mono text-xs text-slate-400">${c.no_hp}</span>`;
                results.appendChild(li);
            });
        }
        results.classList.remove('hidden');
    },

    async selectCustomer(id, name) {
        document.getElementById('order-customer-search').value = name;
        document.getElementById('order-customer-id').value = id;
        document
            .getElementById('order-customer-results')
            .classList.add('hidden');
        document.getElementById('order-new-device-customer').value = id;

        const devices = await this.loadDevices(id);

        if (devices.length) {
            document
                .getElementById('order-device-container')
                .classList.remove('hidden');
            document.getElementById('order-new-device').classList.add('hidden');
            fillSelect(
                document.getElementById('order-device-id'),
                devices.map((d) => ({
                    value: d.id,
                    label: `${d.jenis.toUpperCase()} ${d.merk} ${d.model ?? ''} — ${d.keluhan.slice(0, 40)}`,
                })),
                'Pilih perangkat…',
            );
        } else {
            document
                .getElementById('order-device-container')
                .classList.remove('hidden');
            document
                .getElementById('order-new-device')
                .classList.remove('hidden');
            fillSelect(
                document.getElementById('order-device-id'),
                [],
                'Belum ada perangkat',
            );
        }
    },

    async loadDevices(customerId) {
        const { devices } = await ajax.get(
            `/api/customers/${customerId}/devices`,
        );
        return devices;
    },

    async submit(e) {
        e.preventDefault();
        clearErrors('order-errors');

        const orderId = document.getElementById('order-id').value;
        const submitBtn = document.getElementById('order-submit-btn');
        submitBtn.disabled = true;

        try {
            let deviceId = document.getElementById('order-device-id').value;
            const customerId =
                document.getElementById('order-customer-id').value;

            if (
                !deviceId &&
                customerId &&
                !document
                    .getElementById('order-new-device')
                    .classList.contains('hidden')
            ) {
                const customer_id = Number(
                    document.getElementById('order-new-device-customer').value,
                );
                const { device } = await ajax.post('/devices', {
                    customer_id,
                    jenis: document.getElementById('order-device-jenis').value,
                    merk: document.getElementById('order-device-merk').value,
                    model: document.getElementById('order-device-model').value,
                    keluhan: document.getElementById('order-device-keluhan')
                        .value,
                });
                deviceId = device.id;
            }

            const payload = {
                device_id: Number(deviceId),
                status: document.getElementById('order-status').value,
                teknisi_id: document.getElementById('order-tech').value
                    ? Number(document.getElementById('order-tech').value)
                    : null,
                estimasi_biaya: document.getElementById('order-estimate').value
                    ? Number(document.getElementById('order-estimate').value)
                    : null,
                tanggal_masuk: new Date().toISOString().slice(0, 10),
            };

            if (orderId) {
                await ajax.put(`/service-orders/${orderId}`, payload);
            } else {
                await ajax.post('/service-orders', payload);
            }

            closeModal('modal-order-form');
            toast(orderId ? 'Tiket diperbarui.' : 'Tiket berhasil dibuat.');
            refreshOrders();
        } catch (err) {
            showErrors('order-errors', err.errors ?? { error: [err.message] });
            submitBtn.disabled = false;
        }
    },
};

// Status select delegation --------------------------------------------------

document.addEventListener('change', async (event) => {
    const select = event.target.closest('[data-status-select]');
    if (!select) return;

    const row = select.closest('[data-order-id]');
    const orderId = row?.dataset.orderId ?? select.dataset.orderId;

    if (!orderId) return;

    try {
        await ajax.patch(`/service-orders/${orderId}/status`, {
            status: select.value,
        });
        toast('Status diperbarui.');
        refreshOrders();
    } catch {
        toast('Gagal memperbarui status.', 'error');
    }
});

// Detail modal status save ---------------------------------------------------

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-detail-status-save]');
    if (!button) return;

    const select = document.getElementById('detail-status-select');
    const orderId = window.currentOrderId;

    if (!orderId) return;

    button.disabled = true;

    try {
        await ajax.patch(`/service-orders/${orderId}/status`, {
            status: select.value,
        });
        toast('Status tiket diperbarui.');
        refreshOrders();
    } catch {
        toast('Gagal memperbarui status.', 'error');
        button.disabled = false;
    }
});

// Init ----------------------------------------------------------------------

const refreshOrders = createListSwapper({
    input: 'order-search',
    results: 'order-results',
    total: 'order-total',
    url: '/service-orders/table',
    loading:
        '<p class="mt-5 py-14 text-center text-sm text-slate-400">Mencari&hellip;</p>',
});

document.addEventListener('repairstation:ready', async () => {
    await orderForm.init();
});

window.RepairStation = {
    ...window.RepairStation,
    openOrderDetail,
    reSendNotification: async (orderId) => {
        try {
            const { message } = await ajax.post(
                `/service-orders/${orderId}/notify`,
            );
            toast(message);
        } catch {
            toast('Gagal mengirim notifikasi.', 'error');
        }
    },
    deleteOrder: async (orderId) => {
        const { isConfirmed } = await swalConfirm(
            'Hapus tiket ini?',
            'Data tiket akan dihapus permanen.',
            'Ya, hapus',
            'error',
        );
        if (!isConfirmed) return;
        await ajax.delete(`/service-orders/${orderId}`);
        toast('Tiket dihapus.');
        refreshOrders();
    },
    confirmLogout: (event, form) => {
        event.preventDefault();

        swalConfirm(
            'Keluar dari aplikasi?',
            'Sesi Anda akan diakhiri.',
            'Ya, keluar',
            'error',
        ).then(({ isConfirmed }) => {
            if (isConfirmed) form.submit();
        });
    },
};
