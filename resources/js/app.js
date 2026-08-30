import Alpine from 'alpinejs';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Alpine = Alpine;
Alpine.start();

import('./modules/common.js').then(async () => {
    const page = document.body.dataset.page;

    if (page) {
        try {
            await import(`./modules/${page}.js`);
        } catch {
            // modul halaman tidak tersedia — abaikan
        }
    }

    if (document.getElementById('invoice-form')) {
        await import('./modules/invoices.js');
    }

    document.dispatchEvent(new CustomEvent('repairstation:ready'));
});