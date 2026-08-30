<?php

use App\Models\Setting;
use Illuminate\Support\Carbon;

if (! function_exists('setting')) {
    function setting(string $key, ?string $default = null): ?string
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('rupiah')) {
    function rupiah(int|float|string|null $amount): string
    {
        return 'Rp '.number_format((int) $amount, 0, ',', '.');
    }
}

if (! function_exists('tanggal')) {
    function tanggal(string|DateTimeInterface|null $date, string $format = 'd/m/Y'): string
    {
        return $date
            ? Carbon::parse($date)->translatedFormat($format)
            : '';
    }
}

if (! function_exists('logo_url')) {
    function logo_url(): ?string
    {
        $path = setting('logo');

        return $path ? asset('storage/'.$path) : null;
    }
}
