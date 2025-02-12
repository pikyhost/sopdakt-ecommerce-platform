<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageProductFeature extends Model
{
    protected $guarded = [];

    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }
}
