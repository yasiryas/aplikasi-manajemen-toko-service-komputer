import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const showDiagnostic = (message) => {
    const banner = document.createElement('div');
    banner.className =
        'fixed bottom-4 left-4 z-[70] max-w-md rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 shadow-xl';
    banner.textContent = message;
    banner.id = 'app-diagnostic';
    const existing = document.getElementById('app-diagnostic');
    existing?.remove();
    document.body.appendChild(banner);
    setTimeout(() => banner.remove(), 8000);
};

window.addEventListener('error', (event) => {
    console.error(event.error ?? event.message);
    showDiagnostic(`Error JS: ${event.message ?? 'tidak diketahui'}`);
});

const pageModules = ['dashboard', 'customers', 'devices', 'invoices'];

import('./modules/common.js').then(async () => {
    const page = document.body.dataset.page;

    if (pageModules.includes(page)) {
        try {
            await import(`./modules/${page}.js`);
        } catch (error) {
            console.error(`Modul ${page}.js gagal dimuat:`, error);
            showDiagnostic(
                `Modul halaman (${page}) gagal dimuat: ${error.message ?? error}`,
            );
        }
    }

    if (document.getElementById('invoice-form')) {
        await import('./modules/invoices.js');
    }

    document.dispatchEvent(new CustomEvent('repairstation:ready'));
});
