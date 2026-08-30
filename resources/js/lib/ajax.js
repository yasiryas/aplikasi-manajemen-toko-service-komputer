export const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content') ?? '';

function headers(extra = {}) {
    return {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        ...extra,
    };
}

async function request(method, url, body = null, { form = false } = {}) {
    const response = await fetch(url, {
        method,
        headers: form ? { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken } : headers(),
        body: body === null ? undefined : form ? body : JSON.stringify(body),
    });

    const isJson = response.headers.get('content-type')?.includes('application/json');

    const data = isJson ? await response.json() : null;

    if (!response.ok) {
        throw { status: response.status, message: data?.message ?? 'Terjadi kesalahan.', errors: data?.errors, data };
    }

    return data;
}

export const ajax = {
    get: (url) => request('GET', url),
    post: (url, body) => request('POST', url, body),
    put: (url, body) => request('PUT', url, body),
    patch: (url, body) => request('PATCH', url, body),
    delete: (url) => request('DELETE', url),
};

export function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(value ?? 0));
}

export function debounce(fn, delay = 300) {
    let timer;

    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}