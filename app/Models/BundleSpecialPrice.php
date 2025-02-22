<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BundleSpecialPrice extends Model
{
    protected $guarded = [];

    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function countryGroup()
    {
        return $this->belongsTo(CountryGroup::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
