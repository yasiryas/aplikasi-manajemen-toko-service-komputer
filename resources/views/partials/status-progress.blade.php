@props(['label', 'total', 'percent', 'class'])

<div class="rounded-lg border border-slate-100 p-3">
    <div class="mb-1.5 flex items-center justify-between text-sm">
        <span class="font-medium text-slate-600">{{ $label }}</span>
        <span class="font-mono text-xs text-slate-400">{{ $total }}</span>
    </div>
    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
        <div class="h-full rounded-full transition-all duration-700 {{ $class }}" style="width: {{ $percent }}%"></div>
    </div>
</div>