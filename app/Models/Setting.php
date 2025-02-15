<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'array',
    ];

    public static function get($key, $locale = null)
    {
        $setting = self::where('key', $key)->first();
        $value = $setting?->value;

        if ($locale && is_array($value)) {
            return $value[$locale] ?? null;
        }

        return $value;
    }

    public static function set($key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
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
        $setting = self::where('key', $key)->first();
        $value = $setting?->value;

        if ($locale && is_array($value)) {
            return $value[$locale] ?? null;
        }

        return $value;
    }

}
