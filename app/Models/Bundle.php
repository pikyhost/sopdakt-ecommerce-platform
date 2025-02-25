<?php

namespace App\Models;

use App\Enums\BundleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Bundle extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = [
        'name',
    ];

    protected $guarded = [];


    protected $casts = [
        'bundle_type' => BundleType::class,
    ];

    public function mainProduct()
    {
        return $this->belongsTo(Product::class, 'main_product_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'bundle_product')->withPivot('quantity');
    }

    public function getFormattedDiscountAttribute()
    {
        if ($this->bundle_category === 'accessory') {
            return "Accessory Bundle";
        }

        if ($this->bundle_type === 'fixed_price') {
            return "Bundle Price: $" . number_format($this->discount_price, 2);
        }

        if ($this->bundle_type === 'discount_percentage') {
            return "Save {$this->discount_percentage}%";
        }

        if ($this->bundle_type === 'buy_x_get_y') {
            return "Buy {$this->buy_x}, Get {$this->get_y} Free!";
        }

        return "Special Offer";
    }

    public function getTotalPriceAttribute()
    {
        $productsTotal = $this->products->sum(fn($product) => $product->discount_price_for_current_country * $product->pivot->quantity);

        switch ($this->bundle_type) {
            case 'fixed_price':
                return $this->discount_price;

            case 'discount_percentage':
                return $productsTotal * (1 - ($this->discount_percentage / 100));

            case 'buy_x_get_y':
                return ($this->buy_x * $this->products->first()->price); // Assuming first product is used

            default:
                return $productsTotal;
        }
    }

    public function specialPrices()
    {
        return $this->hasMany(BundleSpecialPrice::class);
    }

    public function getBundlePriceForCurrentCountryAttribute()
    {
        return (float) \App\Helpers\GeneralHelper::getBundlePriceForCountry($this);
    }

    public function getBundleDiscountPriceForCurrentCountryAttribute()
    {
        return (float) \App\Helpers\GeneralHelper::getBundlePriceForCountryWithDiscount($this);
    }

}
