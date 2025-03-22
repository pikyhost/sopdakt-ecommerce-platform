<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageOrder extends Model
{
    use HasFactory;

    protected $guarded=[];

    function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }

    function size()
    {
        return $this->belongsTo(Size::class);
    }

    function color()
    {
        return $this->belongsTo(Color::class);
    }

    function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    function shippingType()
    {
        return $this->belongsTo(ShippingType::class);
    }

    function country()
    {
        return $this->belongsTo(Country::class);
    }

    function city()
    {
        return $this->belongsTo(City::class);
    }

    function varieties()
    {
        return $this->hasMany(LandingPageOrderVariety::class);
    }

    function bundle()
    {
        return $this->belongsTo(LandingPageBundle::class,'landing_page_bundle_id');
    }

    public function scopeWhenCreatedAt($query, $createdAt)
    {
        return $query->when($createdAt, function ($q) use ($createdAt) {
            $dates = explode('-', \request('created_at'));
            $start = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->format('Y-m-d');
            $end = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->format('Y-m-d');
            return $q->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end)
                ->orWhereDate('created_at', $start);
        });
    }
}
