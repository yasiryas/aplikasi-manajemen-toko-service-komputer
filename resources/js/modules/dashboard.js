import { ajax } from '../lib/ajax';

function formatRupiah(value) {
    return 'Rp '.concat(
        Number(value).toLocaleString('id-ID').replace(/,/g, '.'),
    );
}

function updateCards(cards) {
    const pendapatan = document.querySelector('[data-stat="pendapatan_hari_ini"]');

    Object.entries(cards).forEach(([key, value]) => {
        const el = document.querySelector(`[data-stat="${key}"]`);

        if (el && key !== 'pendapatan_hari_ini') {
            el.textContent = value;
        }
    });

    if (pendapatan) {
        pendapatan.textContent = formatRupiah(cards.pendapatan_hari_ini);
    }
}

function renderActivity(items) {
    const feed = document.getElementById('activity-feed');

    if (!feed || !items.length) return;

    feed.innerHTML = items
        .map(
            (item) => `<li class="relative">
                <span class="absolute -left-[21px] top-1.5 h-3 w-3 rounded-full border-2 border-white ${item.badge_class.includes('emerald') ? 'bg-emerald-500' : item.badge_class.includes('rose') ? 'bg-rose-500' : item.badge_class.includes('amber') ? 'bg-amber-500' : 'bg-indigo-500'}"></span>
                <div class="flex items-center justify-between gap-2">
                    <span class="font-mono text-xs font-semibold text-indigo-600">${item.no_tiket}</span>
                    <span class="text-xs text-slate-400">${item.created_at}</span>
                </div>
                <p class="mt-1 text-sm text-slate-600">Status berubah menjadi <span class="badge ${item.badge_class}">${item.status_label}</span></p>
                <p class="text-xs text-slate-400">oleh ${item.user}</p>
            </li>`,
        )
        .join('');
}

async function pollStats() {
    try {
        const cards = await ajax.get('/dashboard/stats');
        updateCards(cards);
    } catch {
        // polling senyap: jaringan putus tidak perlu memicing pengguna
    }
}

async function pollActivity() {
    try {
        const { items } = await ajax.get('/dashboard/activity');
        renderActivity(items);
    } catch {
        // polling senyap
    }
}

setInterval(pollStats, 8000);
setInterval(pollActivity, 12000);

document.addEventListener('DOMContentLoaded', () => {
    pollStats();
    pollActivity();
});