@props(['boxClass' => 'h-9 w-9 rounded-lg bg-brand-600 text-white', 'iconClass' => 'fa-microchip text-lg'])

@php($logo = logo_url())

@if ($logo)
    <img src="{{ $logo }}" alt="{{ setting('nama_toko', 'Service Computer') }}" class="{{ $boxClass }} h-auto shrink-0 object-contain object-center p-1">
@else
    <div class="flex {{ $boxClass }} shrink-0 items-center justify-center">
        <i class="fa-solid {{ $iconClass }}"></i>
    </div>
@endif