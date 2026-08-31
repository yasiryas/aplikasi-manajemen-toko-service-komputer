@props(['boxClass' => 'h-9 w-9 rounded-lg'])

@php($logo = logo_url() ?? asset('icons/icon-192.png'))
<img src="{{ $logo }}" alt="{{ setting('nama_toko', 'Service Computer') }}" class="{{ $boxClass }} shrink-0 object-contain p-1" width="36" height="36">