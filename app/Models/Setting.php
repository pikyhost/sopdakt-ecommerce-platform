<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'site_name_en',
        'site_name_ar',
        'currency_id',
        'logo_en',
        'logo_ar',
        'dark_logo_en',
        'dark_logo_ar',
        'favicon_en',
        'favicon_ar',
    ];

    protected static string $cacheKey = 'app_settings';

    protected static function boot()
    {
        parent::boot();

        static::saved(fn () => self::reloadCache());
        static::deleted(fn () => self::reloadCache());
    }

    /**
     * Get all settings from cache or database.
     */
    public static function getAllSettings(): array
    {
        return Cache::rememberForever(self::$cacheKey, function () {
            return self::first()?->toArray() ?? [];
        });
    }

    /**
     * Retrieve a specific setting value.
     */
    public static function getSetting(string $key)
    {
        return self::getAllSettings()[$key] ?? null;
    }

    /**
     * Update settings and refresh the cache.
     */
    public static function updateSettings(array $data): void
    {
        self::firstOrNew()->fill($data)->save();
        self::reloadCache();
    }

    /**
     * Reload settings cache.
     */
    public static function reloadCache(): void
    {
        Cache::forget(self::$cacheKey);
        Cache::forever(self::$cacheKey, self::first()?->toArray() ?? []);
    }

    /**
     * Clear the settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::$cacheKey);
    }

    /**
     * Currency relationship.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the currency with its symbol.
     */
    public static function getCurrency(): ?object
    {
        $settings = self::getAllSettings();
        if (!isset($settings['currency_id'])) {
            return null;
        }

        return Cache::rememberForever("currency_{$settings['currency_id']}", function () use ($settings) {
            return \App\Models\Currency::find($settings['currency_id']);
        });
    }

}
