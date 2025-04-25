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
        'minimum_stock_level',

        // New fields
        'primary_color',
        'secondary_color',
        'enable_aramex',
        'enable_bosta',
        'enable_jnt',
    ];

    protected static string $cacheKey = 'app_settings';

    /**
     * Boot method to clear cache on updates.
     */
    protected static function boot(): void
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
    public static function getSetting(string $key): mixed
    {
        $settings = self::getAllSettings();

        if (!array_key_exists($key, $settings)) {
            return null;
        }

        return $settings[$key];
    }

    /**
     * Update settings and refresh the cache.
     */
    public static function updateSettings(array $data): bool
    {
        try {
            $settings = self::firstOrNew();
            $settings->fill($data);
            $settings->save();
            return true;
        } catch (\Exception $e) {
            logger()->error('Failed to update settings: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reload settings cache and clear related cached values.
     */
    public static function reloadCache(): void
    {
        Cache::forget(self::$cacheKey);

        $settings = self::query()->first()?->toArray() ?? [];
        Cache::forever(self::$cacheKey, $settings);

        // Clear related caches
        if (isset($settings['currency_id'])) {
            Cache::forget("currency_{$settings['currency_id']}");
        }
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
    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Country relationship.
     */
    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the currency with its symbol.
     */
    public static function getCurrency(): ?Currency
    {
        $settings = self::getAllSettings();

        if (empty($settings['currency_id'])) {
            return null;
        }

        return Cache::rememberForever("currency_{$settings['currency_id']}", function () use ($settings) {
            return Currency::find($settings['currency_id']);
        });
    }

    /**
     * Get the tax percentage from settings with caching.
     */
    public static function getTaxPercentage(): float
    {
        return (float)(self::getAllSettings()['tax_percentage'] ?? 0.0);
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

    /**
     * Check if shipping type is enabled.
     */
    public static function isShippingEnabled(): bool
    {
        return (bool) self::getSetting('shipping_type_enabled');
    }

    /**
     * Check if shipping locations are enabled.
     */
    public static function isShippingLocationsEnabled(): bool
    {
        return (bool) self::getSetting('shipping_locations_enabled');
    }

    /**
     * Get the minimum stock level setting.
     */
    public static function getMinimumStockLevel(): int
    {
        return (int) self::getSetting('minimum_stock_level');
    }

    /**
     * Check if Aramex integration is enabled.
     */
    public static function isAramexEnabled(): bool
    {
        return (bool) self::getSetting('enable_aramex');
    }

    /**
     * Check if Bosta integration is enabled.
     */
    public static function isBostaEnabled(): bool
    {
        return (bool) self::getSetting('enable_baosta');
    }

    /**
     * Check if J&T integration is enabled.
     */
    public static function isJntEnabled(): bool
    {
        return (bool) self::getSetting('enable_jnt');
    }

    /**
     * Get the theme color settings.
     */
    public static function getThemeColors(): array
    {
        return [
            'primary' => self::getSetting('primary_color'),
            'secondary' => self::getSetting('secondary_color'),
        ];
    }

    public static function isEnabled(string $key): bool
    {
        return self::where('key', $key)->value('value') == '1';
    }

}
