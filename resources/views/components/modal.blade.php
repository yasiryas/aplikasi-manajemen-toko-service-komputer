@props(['id', 'title' => '', 'maxWidth' => 'md:max-w-lg'])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden items-start justify-center overflow-y-auto p-4 pt-[12vh]" x-cloak>
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" data-close-modal></div>

    <div class="relative w-full {{ $maxWidth }} rounded-2xl bg-white shadow-2xl" role="dialog" aria-modal="true" aria-label="{{ $title }}">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="font-heading text-base font-bold text-slate-900">{{ $title }}</h3>
            <button type="button" class="btn-icon h-8 w-8 text-slate-400 hover:bg-slate-100 hover:text-slate-600" data-close-modal aria-label="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="px-5 py-4">
            {{ $slot }}
        </div>
    </div>
</div>