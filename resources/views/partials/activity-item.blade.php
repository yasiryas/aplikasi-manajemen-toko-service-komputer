@foreach ($items as $item)
    <li class="relative">
        <span class="absolute -left-[21px] top-1.5 h-3 w-3 rounded-full border-2 border-white {{ Str::contains($item['badge_class'], 'emerald') ? 'bg-emerald-500' : (Str::contains($item['badge_class'], 'rose') ? 'bg-rose-500' : (Str::contains($item['badge_class'], 'amber') ? 'bg-amber-500' : 'bg-indigo-500')) }}"></span>
        <div class="flex items-center justify-between gap-2">
            <span class="font-mono text-xs font-semibold text-indigo-600">{{ $item['no_tiket'] }}</span>
            <span class="text-xs text-slate-400">{{ $item['created_at'] }}</span>
        </div>
        <p class="mt-1 text-sm text-slate-600">
            Status berubah menjadi <span class="badge {{ $item['badge_class'] }}">{{ $item['status_label'] }}</span>
        </p>
        <p class="text-xs text-slate-400">oleh {{ $item['user'] }}</p>
    </li>
@endforeach