<?php

namespace App\Models;

use App\Enums\BundleType;
use App\Helpers\GeneralHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;

class Bundle extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];

    protected $guarded = [];

    protected $casts = [
        'bundle_type' => BundleType::class,
    ];

    public function specialPrices()
    {
        return $this->hasMany(BundleSpecialPrice::class);
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
        return match ($this->bundle_type) {
            'fixed_price' => "Bundle Price: $" . number_format($this->discount_price, 2),
            'buy_x_get_y' => "Buy {$this->buy_x}, Get {$this->get_y} Free!",
            'buy_quantity_fixed_price' => "Buy {$this->buy_x} for $" . number_format($this->discount_price, 2),
            default => "Special Offer",
        };
    }

    public function getTotalPriceAttribute()
    {
        $productsTotal = $this->products->sum(fn($product) => $product->discount_price_for_current_country);

        return match ($this->bundle_type) {
            'fixed_price' => $this->discount_price,
            'buy_x_get_y' => $this->buy_x * optional($this->products->first())->price,
            'buy_quantity_fixed_price' => $this->discount_price,
            default => $productsTotal,
        };
    }

    public function getBundlePriceForCurrentCountryAttribute()
    {
        return GeneralHelper::getBundlePriceForCountry($this);
    }

    public function getBundleDiscountPriceForCurrentCountryAttribute()
    {
        return GeneralHelper::getBundlePriceForCountryWithDiscount($this);
    }

    public function formatPrice(): string
    {
        $countryId = GeneralHelper::getCountryId();
        $currency = Setting::getCurrency()?->code ?? 'USD';
        $amount = $this->discount_price ?? 0;

        $specialPrice = BundleSpecialPrice::where('bundle_id', $this->id)
            ->where(function ($query) use ($countryId) {
                $query->where('country_id', $countryId)
                    ->orWhereNull('country_id')
                    ->whereExists(fn ($existsQuery) => $existsQuery->select(DB::raw(1))
                        ->from('country_group_country')
                        ->whereRaw('bundle_special_prices.country_group_id = country_group_country.country_group_id')
                        ->where('country_group_country.country_id', $countryId)
                    );
            })
            ->orderByRaw('CASE WHEN country_id IS NOT NULL THEN 1 ELSE 2 END')
            ->first(['special_price', 'special_price_after_discount', 'currency_id']);

        if ($specialPrice) {
            $amount = $specialPrice->special_price_after_discount ?? $specialPrice->special_price;
            $currency = $specialPrice?->currency_id
                ? Currency::find($specialPrice->currency_id)?->code
                : $currency;
        }

        return GeneralHelper::formatBundlePrice($amount, $currency);
    }

    /**
     * Calculate the discount price for BUY_X_GET_Y bundles.
     */
    private static function calculateDiscountPrice($bundle)
    {
        $bundleType = $bundle->bundle_type->value ?? null;

        if ($bundleType === BundleType::BUY_X_GET_Y->value && $bundle->buy_x) {
            $firstProduct = $bundle->products()->first();

            if ($firstProduct && !is_null($firstProduct->discount_price_for_current_country)) {
                $bundle->discount_price = $bundle->buy_x * floatval($firstProduct->discount_price_for_current_country);
            }
        }
    }
}
