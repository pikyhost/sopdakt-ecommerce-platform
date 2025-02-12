<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageGovernorate extends Model
{
    protected $guarded = [];
    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

}
