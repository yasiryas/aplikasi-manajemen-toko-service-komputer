@props(['align' => 'right', 'width' => 'w-48'])

<div {{ $attributes->merge(['class' => 'relative']) }} x-data="{ open: false }" @click.outside="open = false">
    <div @click="open = !open">{{ $trigger }}</div>

    <div x-show="open" x-cloak x-transition
        class="absolute mt-2 {{ $align === 'right' ? 'right-0' : 'left-0' }} {{ $width }} rounded-lg border border-slate-200 bg-white p-1.5 shadow-lg">
        {{ $slot }}
    </div>
</div>
