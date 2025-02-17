<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCost extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the product associated with this shipping cost.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shippingType()
    {
        return $this->belongsTo(ShippingType::class);
    }

    /**
     * Get the city associated with this shipping cost.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the governorate associated with this shipping cost.
     */
    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    /**
     * Get the shipping zone associated with this shipping cost.
     */
    public function shippingZone()
    {
        return $this->belongsTo(ShippingZone::class);
    }

    /**
     * Get the country associated with this shipping cost.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the country group associated with this shipping cost.
     */
    public function countryGroup()
    {
        return $this->belongsTo(CountryGroup::class);
    }

    /**
     * Get the best location-based priority for the shipping cost.
     *
     * @return string
     */
    public function getPriorityAttribute()
    {
        if ($this->city_id) {
            return 'City';
        } elseif ($this->governorate_id) {
            return 'Governorate';
        } elseif ($this->shipping_zone_id) {
            return 'Shipping Zone';
        } elseif ($this->country_id) {
            return 'Country';
        } elseif ($this->country_group_id) {
            return 'Country Group';
        }

        return 'Unknown';
    }
}
