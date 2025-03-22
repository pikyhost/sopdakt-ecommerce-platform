<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HomePageSetting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'main_heading',
        'discount_text',
        'discount_value',
        'starting_price',
        'currency_symbol',
        'button_text',
        'button_url',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(fn() => self::clearCache());
        static::deleted(fn() => self::clearCache());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('slider1_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('slider2_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        if (!$media) {
            return;
        }

        if ($media->collection_name === 'slider1_image') {
            $this->addMediaConversion('slider1_thumb')
                ->width(400)
                ->height(250)
                ->sharpen(10);
        }

        if ($media->collection_name === 'slider2_image') {
            $this->addMediaConversion('slider2_thumb')
                ->width(400)
                ->height(250)
                ->sharpen(10);
        }
    }


    // Get original & thumbnail URLs
    public function getSlider1ImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('slider1_image');
    }

    public function getSlider2ImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('slider2_image');
    }

    public function getSlider1ThumbnailUrl(): ?string
    {
        return $this->getFirstMediaUrl('slider1_image', 'slider1_thumb');
    }

    public function getSlider2ThumbnailUrl(): ?string
    {
        return $this->getFirstMediaUrl('slider2_image', 'slider2_thumb');
    }


    public static function getCached()
    {
        return Cache::rememberForever('home_page_settings', fn() => self::first());
    }

    public static function clearCache()
    {
        Cache::forget('home_page_settings');
    }
}
