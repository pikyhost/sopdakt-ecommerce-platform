<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'currency_id'];

    protected $casts = [
        'value' => 'array',
    ];

    // Static property to hold already fetched settings in memory
    protected static array $settingsCache = [];

    public static function get($key, $locale = null)
    {
        // Ensure the table exists before querying
        if (!Schema::hasTable('settings')) {
            return null;
        }

        // Check if the setting is already loaded in memory
        if (!isset(self::$settingsCache[$key])) {
            $setting = self::where('key', $key)->first();
            self::$settingsCache[$key] = $setting?->value ?? null;
        }

        $value = self::$settingsCache[$key];

        if ($locale && is_array($value)) {
            return $value[$locale] ?? null;
        }

        return $value;
    }


    public static function set($key, $value)
    {
        $setting = self::updateOrCreate(['key' => $key], ['value' => $value]);
        self::$settingsCache[$key] = $value; // Update static cache to prevent re-querying
        return $setting;
    }

    public function value(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true) ?? [],
            set: fn ($value) => json_encode($value)
        );
    }

    public static function getSetting($key, $locale = null)
    {
        return self::get($key, $locale); // Reuse the optimized `get` method
    }


    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
