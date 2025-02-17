<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    public $translatable = [
        'name',
        'description',
        'summary',
        'meta_title',
        'meta_description',
        'custom_attributes'
    ];

    protected $casts = [
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
        'saved_at' => 'datetime',
        'rating_status' => Status::class,
        'custom_attributes' => 'array', // Cast
    ];

    protected $guarded = [];

    protected static function booted()
    {
        // Create inventory record when a product is created
        static::created(function (Product $product) {
            Inventory::create([
                'product_id' => $product->id,
                'quantity'   => $product->quantity,
            ]);
        });

        // Update inventory when product quantity changes
        static::updated(function (Product $product) {
            if ($product->wasChanged('quantity')) {
                Inventory::where('product_id', $product->id)
                    ->update(['quantity' => $product->quantity]);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'label_product');
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_sizes');
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class, 'product_colors');
    }

    public function colorsWithImages()
    {
        return $this->hasMany(ProductColor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function shippingTypes()
    {
        return $this->belongsToMany(ShippingType::class)->withPivot(['shipping_cost', 'status']);
    }

    function shippingZones()
    {
        return $this->belongsToMany(Zone::class,'product_shipping_zone')->withPivot(['shipping_cost', 'status']);
    }

    function shippingGovernorates()
    {
        return $this->belongsToMany(Governorate::class,'product_governorate')->withPivot(['shipping_cost', 'status']);
    }

    function shippingRegions()
    {
        return $this->belongsToMany(Region::class,'product_region')->withPivot(['shipping_cost', 'status']);
    }

    public function specialPrices()
    {
        return $this->hasMany(ProductSpecialPrice::class, 'product_id', 'id');
    }


    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function getStockAttribute()
    {
        return $this->inventory ? $this->inventory->quantity : 0;
    }

    public function ratings()
    {
        return $this->hasMany(ProductRating::class);
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('feature_product_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this
            ->addMediaCollection('second_feature_product_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this
            ->addMediaCollection('sizes_image')
            ->singleFile();

        $this
            ->addMediaCollection('more_product_images_and_videos')
            ->acceptsMimeTypes(['video/mp4', 'video/mpeg', 'video/quicktime',
                'image/jpeg', 'image/png', 'image/webp']);
    }

    public function getFeatureProductImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('feature_product_image') ?: null;
    }


    /**
     * Get the URL for the 'main_author_image' .
     */
    public function getSecondFeatureProductImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('second_feature_product_image') ?: null;
    }

    /**
     * Get the URL for the 'main_author_image' .
     */
    public function getProductSizeImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('sizes_image') ?: null;
    }


    public function getMoreProductImagesAndVideosUrls(string $conversion = 'medium'): array
    {
        return $this->getMedia('more_product_images_and_videos')
            ->map(fn ($media) => $media->getUrl($conversion))
            ->toArray();
    }

    public function usersWhoSaved(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_products', 'product_id', 'user_id')
            ->withTimestamps()
            ->withPivot('created_at as saved_at');
    }

    public function getPriceForCurrentCountryAttribute()
    {
        return \App\Helpers\GeneralHelper::getPriceForCountry($this);
    }

    public function getDiscountPriceForCurrentCountryAttribute()
    {
        return \App\Helpers\GeneralHelper::getPriceForCountryWithDiscount($this);
    }

    public function getRouteKeyName()
    {
        return 'slug'; // This allows Laravel to automatically resolve routes by slug instead of ID
    }

    public function bundles()
    {
        return $this->belongsToMany(Bundle::class, 'bundle_product')->withPivot('quantity');
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class)->withPivot('value');
    }

    public function shippingCosts()
    {
        return $this->hasMany(ShippingCost::class);
    }
}
