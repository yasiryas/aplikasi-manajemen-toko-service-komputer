@props(['icon', 'label', 'value', 'hint', 'accent' => 'text-indigo-600', 'iconBg' => 'bg-indigo-100'])

<div class="card p-4 md:p-5">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm text-slate-500">{{ $label }}</p>
            <p class="mt-1 font-heading text-2xl font-bold text-slate-900 md:text-3xl">{!! $value !!}</p>
            @if (isset($hint))
                <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
            @endif
        </div>
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $iconBg }} {{ $accent }}">
            <i class="fa-solid {{ $icon }}"></i>
        </div>
    </div>
</div>