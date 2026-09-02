const TOAST_DURATION = 2600;

let toastTimer = null;
const dialogStack = [];

function setBodyLock(locked) {
    document.body.style.overflow = locked ? 'hidden' : '';
}

function toast(message, type = 'success') {
    const kind = type === 'error' ? 'error' : 'success';
    const icon =
        kind === 'error' ? 'fa-triangle-exclamation' : 'fa-circle-check';
    const accent = kind === 'error' ? 'text-rose-600' : 'text-emerald-600';

    const existing = document.getElementById('app-toast');
    if (existing) existing.remove();

    const el = document.createElement('div');
    el.id = 'app-toast';
    el.setAttribute('role', 'status');
    el.className =
        'fixed right-4 top-4 z-[60] flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-xl';

    el.innerHTML = `
        <i class="fa-solid ${icon} ${accent}"></i>
        <p class="text-sm font-medium text-slate-800">${message}</p>
    `;

    document.body.appendChild(el);
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => el.remove(), TOAST_DURATION);
}

function mountDialog(content, resolve) {
    const wrapper = document.createElement('div');
    wrapper.className =
        'fixed inset-0 z-[55] flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm';
    wrapper.innerHTML = content;
    document.body.appendChild(wrapper);
    setBodyLock(true);
    dialogStack.push({ wrapper, resolve });
    return wrapper;
}

function closeTopDialog(cancelValue = null) {
    const entry = dialogStack.pop();
    if (!entry) return;

    entry.wrapper.remove();
    if (!dialogStack.length) setBodyLock(false);
    entry.resolve(cancelValue);
}

function confirmDialog({
    title,
    text,
    confirmText = 'Ya, lanjutkan',
    danger = false,
}) {
    return new Promise((resolve) => {
        const wrapper = mountDialog(
            `
            <div class="relative max-h-[80vh] w-full max-w-sm overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl" role="dialog" aria-modal="true">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full ${danger ? 'bg-rose-100 text-rose-600' : 'bg-indigo-100 text-indigo-600'}">
                        <i class="fa-solid ${danger ? 'fa-trash-can' : 'fa-circle-question'}"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-heading text-base font-bold text-slate-900">${title}</h3>
                        <p class="mt-1 text-sm text-slate-500">${text}</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" class="btn-secondary" data-dialog-cancel>Batal</button>
                    <button type="button" class="${danger ? 'btn-danger' : 'btn-primary'}" data-dialog-confirm>${confirmText}</button>
                </div>
            </div>
        `,
            (value) => resolve(Boolean(value)),
        );

        const dialog = wrapper.firstElementChild;

        wrapper.addEventListener('click', (event) => {
            if (event.target === wrapper) return closeTopDialog();
            if (event.target.closest('[data-dialog-confirm]')) {
                return closeTopDialog(true);
            }
            if (event.target.closest('[data-dialog-cancel]')) {
                return closeTopDialog();
            }
        });

        dialog.querySelector('[data-dialog-confirm]')?.focus();
    });
}

function selectPrompt({
    title,
    text,
    options,
    placeholder = 'Pilih…',
    confirmText = 'Simpan',
}) {
    return new Promise((resolve) => {
        const optionsHtml = Object.entries(options)
            .map(
                ([value, label]) =>
                    `<option value="${value}">${label}</option>`,
            )
            .join('');

        const wrapper = mountDialog(
            `
            <div class="relative max-h-[80vh] w-full max-w-sm overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl" role="dialog" aria-modal="true">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <h3 class="font-heading text-base font-bold text-slate-900">${title}</h3>
                        <p class="mt-1 text-sm text-slate-500">${text}</p>
                    </div>
                    <button type="button" class="btn-icon h-8 w-8 shrink-0 text-slate-400 hover:bg-slate-100" data-dialog-cancel aria-label="Tutup">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <select data-dialog-value class="input mt-4">
                    <option value="" disabled selected>${placeholder}</option>
                    ${optionsHtml}
                </select>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" class="btn-secondary" data-dialog-cancel>Batal</button>
                    <button type="button" class="btn-primary" data-dialog-confirm>${confirmText}</button>
                </div>
            </div>
        `,
            resolve,
        );

        const dialog = wrapper.firstElementChild;
        const select = dialog.querySelector('[data-dialog-value]');

        const confirm = () => {
            const value = select.value;
            if (!value) return;
            closeTopDialog(value);
        };

        wrapper.addEventListener('click', (event) => {
            if (event.target === wrapper) return closeTopDialog();
            if (event.target.closest('[data-dialog-confirm]')) return confirm();
            if (event.target.closest('[data-dialog-cancel]')) {
                return closeTopDialog();
            }
        });

        select.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') confirm();
        });

        setTimeout(
            () => dialog.querySelector('[data-dialog-value]')?.focus(),
            0,
        );
    });
}

function closeAllDialogs() {
    while (dialogStack.length) closeTopDialog();
}

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    closeTopDialog();
});

export { toast, confirmDialog, selectPrompt, closeAllDialogs };
