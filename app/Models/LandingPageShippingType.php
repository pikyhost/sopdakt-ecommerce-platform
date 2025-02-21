<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageShippingType extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'landing_page_id',
        'shipping_type_id',
        'shipping_cost',
        'status',
        'created_at',
        'updated_at',
    ];
}
