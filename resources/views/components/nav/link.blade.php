@props(['href', 'active' => false])

<a href="{{ $href }}" {{ $attributes->merge(['class' => $active
    ? 'flex items-center gap-3 rounded-lg bg-brand-600 px-3 py-2.5 text-sm font-semibold text-white'
    : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900']) }}
    :class="sidebarCollapsed ? 'md:justify-center md:px-0' : ''">
    {{ $icon }}
    <span x-show="!sidebarCollapsed" x-cloak>{{ $slot }}</span>
</a>