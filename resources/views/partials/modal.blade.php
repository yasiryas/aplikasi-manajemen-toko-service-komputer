@props(['id', 'title', 'maxWidth' => 'md:max-w-lg'])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden items-end justify-center md:flex">
    <div class="absolute inset-0 bg-slate-900/50" data-close-modal></div>
    <div class="relative z-10 w-full max-h-[92vh] overflow-y-auto rounded-t-2xl bg-white p-5 shadow-xl {{ $maxWidth }} md:rounded-2xl">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-heading text-lg font-bold text-slate-900">{{ $title }}</h3>
            <button type="button" class="btn-icon" data-close-modal aria-label="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        {{ $slot }}
    </div>
</div>