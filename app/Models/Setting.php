<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 */
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    public static function get(string $key, ?string $default = null): ?string
    {
        $value = self::cacheStore()[$key] ?? null;

        return $value ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        self::forgetCache();
    }

    /**
     * @return array<string, string>
     */
    private static function cacheStore(): array
    {
        return Cache::rememberForever('app_settings', fn () => self::query()->pluck('value', 'key')->all());
    }

    public static function forgetCache(): void
    {
        Cache::forget('app_settings');
    }
}
