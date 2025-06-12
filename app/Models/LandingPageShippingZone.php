<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageShippingZone extends Model
{
    protected $primaryKey = 'shipping_zone_id';
    public $incrementing = false;

    protected $fillable = [
        'landing_page_id',
        'shipping_zone_id',
        'shipping_type_id',
        'shipping_cost',
        'status',
        'created_at',
        'updated_at',
    ];

    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }

    public function shippingZone()
    {
        return $this->belongsTo(Zone::class);
    }

}
