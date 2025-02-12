<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function governorates()
    {
        return $this->belongsToMany(Governorate::class, 'zone_governorates');
    }

    public function shippingTypes()
    {
        return $this->belongsToMany(ShippingType::class, 'shipping_type_zones', 'zone_id', 'shipping_type_id')->withPivot(['shipping_cost','status']);
    }


}
