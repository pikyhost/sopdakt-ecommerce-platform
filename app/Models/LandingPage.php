<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LandingPage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->slug = Str::slug($model->slug ?? $model->home_title);
        });

        self::updating(function($model){
            $model->slug = Str::slug($model->slug ?? $model->home_title);
        });
    }

    function media()
    {
        return $this->hasMany(LandingPageProductMedia::class);
    }

    function features()
    {
        return $this->hasMany(LandingPageProductFeature::class);
    }

    function aboutItems()
    {
        return $this->hasMany(LandingPageAboutSectionItem::class);
    }

    function featuresItems()
    {
        return $this->hasMany(LandingPageFeaturesSectionItem::class);
    }

    function dealOfTheWeekItems()
    {
        return $this->hasMany(LandingPageDealOfTheWeekSection::class);
    }

    function productsItems()
    {
        return $this->hasMany(LandingPageProductsSectionItem::class);
    }

    function whyChooseUsItems()
    {
        return $this->hasMany(LandingPageWhyChooseUsSectionItem::class);
    }

    function feedbacksItems()
    {
        return $this->hasMany(LandingPageFeedbacksSectionItem::class);
    }

    function comparesItems()
    {
        return $this->hasMany(LandingPageComparesSectionItem::class);
    }

    function faqsItems()
    {
        return $this->hasMany(LandingPageFaqsSectionItem::class);
    }

    function varieties()
    {
        return $this->hasMany(LandingPageVarieties::class);
    }

    function colors()
    {
        return $this->belongsToMany(Color::class, 'landing_page_varieties', 'landing_page_id', 'color_id');
    }

    function sizes()
    {
        return $this->belongsToMany(Size::class, 'landing_page_varieties', 'landing_page_id', 'size_id');
    }

    function orders()
    {
        return $this->hasMany(LandingPageOrder::class);
    }

    function topBars()
    {
        return $this->hasMany(LandingPageTopBar::class);
    }

    function LandingPageShippingZones(): HasMany
    {
        return $this->hasMany(LandingPageShippingZone::class);
    }

    function LandingPageShippingTypes(): HasMany
    {
        return $this->hasMany(LandingPageShippingType::class);
    }

    function LandingPageGovernorates(): HasMany
    {
        return $this->hasMany(LandingPageGovernorate::class);
    }

    function LandingPageRegions(): HasMany
    {
        return $this->hasMany(LandingPageRegion::class);
    }

    function shippingTypes()
    {
        return $this->belongsToMany(ShippingType::class, 'landing_page_shipping_types')->withPivot(['shipping_cost', 'status']);
    }

    function shippingZones()
    {
        return $this->belongsToMany(Zone::class, 'landing_page_shipping_zones')->withPivot(['shipping_cost', 'status']);
    }

    function shippingGovernorates()
    {
        return $this->belongsToMany(Governorate::class, 'landing_page_governorates')->withPivot(['shipping_cost', 'status']);
    }

    function shippingRegions()
    {
        return $this->belongsToMany(Region::class, 'landing_page_regions')->withPivot(['shipping_cost', 'status']);
    }

    function shippingCost($region, ShippingType $shippingType =null): float|null
    {
        $shippingCost = 0;

        $shippingRegion = $this->shippingRegions()
            ->where('landing_page_regions.status', 1)
            ->where('city_id', $region->id)
            ->where('shipping_type_id', $shippingType?->id)
            ->first();


        if ($shippingRegion && $shippingRegion->pivot->shipping_cost) {
            return $shippingRegion->pivot->shipping_cost;
        } else {
            $shippingGovernorate = $this->shippingGovernorates()
                ->where('landing_page_governorates.status', 1)
                ->where('governorate_id', $region->governorate_id)
                ->where('shipping_type_id', $shippingType?->id)
                ->first();

            if ($shippingGovernorate && $shippingGovernorate->pivot->shipping_cost) {
                $shippingCost = $shippingGovernorate->pivot->shipping_cost;
            } else {
                $shippingZone = $this->shippingZones()
                    ->where('landing_page_shipping_zones.status', 1)
                    ->where('zone_id', $region->governorate->zone?->id)
                    ->where('shipping_type_id', $shippingType?->id)
                    ->first();
                if ($shippingZone && $shippingZone->pivot->shipping_cost) {
                    $shippingCost = $shippingZone->pivot->shipping_cost;
                } else {
                    $shippingType = $this->shippingTypes()
                        ->where('landing_page_shipping_types.status', 1)
                        ->where('landing_page_shipping_types.shipping_type_id', $shippingType?->id)
                        ->whereNotNull('landing_page_shipping_types.shipping_cost')
                        ->first();

                    if ($shippingType) {
                        $shippingCost = $shippingType->pivot->shipping_cost;
                    } else {
                        if ($region->shipping_cost) {
                            $shippingCost = $region->shipping_cost;
                        } else {
                            $governorate = $region->governorate;
                            if ($governorate->shipping_cost) {
                                $shippingCost = $governorate->shipping_cost;
                            } else {
                                $zone = $governorate->zone;
                                if ($zone->shipping_cost) {
                                    $shippingCost = $zone->shipping_cost;
                                } else {
                                    if ($shippingType->shipping_cost) {
                                        $shippingCost = $shippingType->shipping_cost;
                                    } else {
                                        $shippingCost = 0;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $shippingCost;
    }

    public function bundles()
    {
        return $this->belongsToMany(Bundle::class, 'bundle_landing_page');
    }
}
