<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HomePageSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_heading',
        'discount_text',
        'discount_value',
        'starting_price',
        'currency_symbol',
        'button_text',
        'button_url',
        'background_image',
        'layer_image',
        'thumbnail_image',
    ];

    protected static function boot()
    {
        parent::boot();

        // Clear and recache settings whenever updated
        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }

    /**
     * Get the cached home page settings.
     *
     * @return HomePageSetting|null
     */
    public static function getCached()
    {
        return Cache::rememberForever('home_page_settings', function () {
            return self::first();
        });
    }

    /**
     * Clear the cached settings.
     */
    public static function clearCache()
    {
        Cache::forget('home_page_settings');
    }
}
