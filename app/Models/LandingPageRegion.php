<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageRegion extends Model
{
    protected $guarded = [];
    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

}
