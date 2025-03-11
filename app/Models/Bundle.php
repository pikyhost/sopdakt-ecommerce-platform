<?php

namespace App\Models;

use App\Enums\BundleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use NumberFormatter;
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


    public function formatPrice($amount)
    {
        $locale = app()->getLocale(); // Get current locale
        $currency = Setting::getCurrency()?->code ?? 'USD'; // Retrieve currency code from settings

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, $currency);
    }


    public function mainProduct()
    {
        return $this->belongsTo(Product::class, 'main_product_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'bundle_product');
    }

    public function landingPages()
    {
        return $this->belongsToMany(LandingPage::class, 'bundle_landing_page');
    }

    public function getFormattedDiscountAttribute()
    {
        if ($this->bundle_type === 'fixed_price') {
            return "Bundle Price: $" . number_format($this->discount_price, 2);
        }

        if ($this->bundle_type === 'buy_x_get_y') {
            return "Buy {$this->buy_x}, Get {$this->get_y} Free!";
        }

        return "Special Offer";
    }

    public function getTotalPriceAttribute()
    {
        $productsTotal = $this->products->sum(fn($product) => $product->discount_price_for_current_country);

        switch ($this->bundle_type) {
            case 'fixed_price':
                return $this->discount_price;

            case 'buy_x_get_y':
                return ($this->buy_x * $this->products->first()->price);

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

    /**
     * Calculate the discount price for BUY_X_GET_Y bundles.
     */
    private static function calculateDiscountPrice($bundle)
    {
        $bundleType = $bundle->bundle_type->value ?? null;

        if ($bundleType === \App\Enums\BundleType::BUY_X_GET_Y->value && $bundle->buy_x) {
            $firstProduct = $bundle->products()->first();

            if ($firstProduct && !is_null($firstProduct->discount_price_for_current_country)) {
                $bundle->discount_price = $bundle->buy_x * floatval($firstProduct->discount_price_for_current_country);
            }
        }
    }

}
