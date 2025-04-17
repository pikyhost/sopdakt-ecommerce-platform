<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = [
        'site_name',
        'currency_id',
        'country_id',
        'tax_percentage',
        'logo_en',
        'logo_ar',
        'dark_logo_en',
        'dark_logo_ar',
        'favicon',
        'phone',
        'email',
        'facebook',
        'youtube',
        'instagram',
        'x',
        'snapchat',
        'tiktok',
        'shipping_type_enabled',
        'shipping_locations_enabled',
        'minimum_stock_level'
    ];

    protected static string $cacheKey = 'app_settings';

    /**
     * Boot method to clear cache on updates.
     */
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
        if (!Schema::hasTable('settings')) {
            return [];
        }

        return Cache::rememberForever(self::$cacheKey, function () {
            return self::query()->first()?->toArray() ?? [];
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
        $settings = self::firstOrNew();
        $settings->fill($data);
        $settings->save();
        self::reloadCache();
    }

    /**
     * Reload settings cache and clear related cached values.
     */
    public static function reloadCache(): void
    {
        Cache::forget(self::$cacheKey);

        $settings = self::query()->first()?->toArray() ?? [];
        Cache::forever(self::$cacheKey, $settings);
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
     * Country relationship.
     */
    public function country()
    {
        return $this->belongsTo(Country::class); // ✅ added
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

    /**
     * Get the tax percentage from settings with caching.
     */
    public static function getTaxPercentage(): float
    {
        return self::getAllSettings()['tax_percentage'] ?? 0.0;
    }

    /**
     * Get contact details (phone & email).
     */
    public static function getContactDetails(): array
    {
        return [
            'phone' => self::getSetting('phone'),
            'email' => self::getSetting('email'),
        ];
    }

    /**
     * Get social media links.
     */
    public static function getSocialMediaLinks(): array
    {
        return [
            'facebook'  => self::getSetting('facebook'),
            'youtube'   => self::getSetting('youtube'),
            'instagram' => self::getSetting('instagram'),
            'x'         => self::getSetting('x'),
            'snapchat'  => self::getSetting('snapchat'),
            'tiktok'    => self::getSetting('tiktok'),
        ];
    }

    public static function isShippingEnabled(): bool
    {
        return (bool) self::getSetting('shipping_type_enabled');
    }

    public static function isShippingLocationsEnabled(): bool
    {
        return (bool) self::getSetting('shipping_locations_enabled');
    }
}
