<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageShippingZone extends Model
{
    protected $guarded = [];

    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }

    public function shippingZone()
    {
        return $this->belongsTo(Zone::class);
    }

}
