<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LandingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'sku', 'slug', 'meta_title', 'meta_description', 'meta_keywords', 'status',
        'is_products', 'product_title', 'product_subtitle', 'products_section_top_image', 'products_section_bottom_image', 'is_products_section_top_image', 'is_products_section_bottom_image',
        'is_why_choose_us', 'why_choose_us_title', 'why_choose_us_subtitle', 'why_choose_us_video', 'why_choose_us_section_top_image', 'why_choose_us_section_bottom_image', 'is_why_choose_us_section_top_image', 'is_why_choose_us_section_bottom_image',
        'is_compare', 'compare_title', 'compare_subtitle', 'compares_section_top_image', 'compares_section_bottom_image', 'is_compares_section_top_image', 'is_compares_section_bottom_image',
        'is_feedbacks', 'feedback_title', 'feedback_subtitle', 'feedbacks_section_top_image', 'feedbacks_section_bottom_image', 'is_feedbacks_section_top_image', 'is_feedbacks_section_bottom_image',
        'is_faq', 'faq_title', 'faq_subtitle', 'faq_image', 'faq_section_top_image', 'faq_section_bottom_image', 'is_faq_section_top_image', 'is_faq_section_bottom_image',
        'is_footer', 'footer_title', 'footer_subtitle', 'footer_image', 'footer_section_top_image', 'footer_section_bottom_image', 'is_footer_section_top_image', 'is_footer_section_bottom_image',
        'is_counter_section', 'counter_section_cta_button_text', 'counter_section_cta_button_link', 'counter_section_end_date',
        'is_about', 'about_title', 'about_subtitle', 'about_section_top_image', 'about_section_bottom_image', 'is_about_section_top_image', 'is_about_section_bottom_image',
        'is_features', 'feature_title', 'feature_subtitle', 'feature_image', 'is_feature_cta_button', 'feature_cta_button_text', 'feature_cta_button_link', 'features3_section_top_image', 'features3_section_bottom_image', 'is_features3_section_top_image', 'is_features3_section_bottom_image',
        'is_features1', 'feature1_title', 'feature1_subtitle', 'feature1_image', 'is_feature1_cta_button', 'feature1_cta_button_text', 'feature1_cta_button_link', 'features1_section_top_image', 'features1_section_bottom_image', 'is_features1_section_top_image', 'is_features1_section_bottom_image',
        'is_features2', 'feature2_title', 'feature2_subtitle', 'feature2_image', 'features2_section_top_image', 'features2_section_bottom_image', 'is_features2_section_top_image', 'is_features2_section_bottom_image', 'is_feature2_cta_button', 'feature2_cta_button_text', 'feature2_cta_button_link',
        'is_home', 'home_image', 'home_title', 'home_subtitle', 'home_discount', 'home_cta_button', 'home_cta_button_text', 'home_cta_button_link', 'home_section_top_image', 'home_section_bottom_image', 'is_home_section_top_image', 'is_home_section_bottom_image',
        'is_deal_of_the_week', 'deal_of_the_week_title', 'deal_of_the_week_subtitle', 'deal_of_the_week_section_top_image', 'deal_of_the_week_section_bottom_image', 'is_deal_of_the_week_section_top_image', 'is_deal_of_the_week_section_bottom_image',
        'title', 'description', 'price', 'after_discount_price', 'rating', 'quantity',
        'header_image', 'contact_us_section_top_image', 'contact_us_section_bottom_image', 'is_contact_us_section_top_image', 'is_contact_us_section_bottom_image',
        'created_at', 'updated_at',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            // dd(request()->all());

            // dd(json_decode(request()->all()['components'][0]['snapshot'])->data->data[0]);

            $model->slug = Str::slug($model->slug ?? $model->home_title);
        });

        self::updating(function($model){
            $model->slug = Str::slug($model->slug ?? $model->home_title);
        });

        self::created(function($model){});
        self::updated(function($model){});
        self::deleting(function($model){});
        self::deleted(function($model){});
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

    function shippingCost(Region $region, ShippingType $shippingType =null): float|null
    {
        $shippingCost = 0;

        $shippingRegion = $this->shippingRegions()
            ->where('landing_page_regions.status', 1)
            ->where('region_id', $region->id)
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
