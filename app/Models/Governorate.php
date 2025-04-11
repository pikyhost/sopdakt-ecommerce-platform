<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Governorate extends Model
{
    use HasTranslations;

    public $translatable = ['name'];

    protected $guarded = [];


    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function shippingTypes()
    {
        return $this->hasMany(ShippingType::class, 'id');
    }

    public function shippingZones()
    {
        return $this->belongsToMany(ShippingZone::class, 'governorate_shipping_zone');
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
