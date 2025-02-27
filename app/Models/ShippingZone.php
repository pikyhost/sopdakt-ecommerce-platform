<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ShippingZone extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name', 'description'];

    protected $guarded = [];

    public function shippingCosts()
    {
        return $this->hasMany(ShippingCost::class);
    }

    public function shippingTypes()
    {
        return $this->hasMany(ShippingType::class, 'id');
    }

    public function governorates()
    {
        return $this->belongsToMany(Governorate::class, 'governorate_shipping_zone');
    }

}
