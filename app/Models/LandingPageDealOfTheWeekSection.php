<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageDealOfTheWeekSection extends Model
{
    use HasFactory;

    function varieties()
    {
        return $this->hasMany(LandingPageDealOfTheWeekVariety::class);
    }
}
