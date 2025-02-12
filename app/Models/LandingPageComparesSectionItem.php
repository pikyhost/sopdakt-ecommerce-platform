<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageComparesSectionItem extends Model
{
    use HasFactory;

    function criteria()
    {
        return $this->hasMany(LandingPageComparesSectionItemCriteria::class);
    }
}
