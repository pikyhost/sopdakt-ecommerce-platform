<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageRegion extends Model
{
    protected $primaryKey = 'region_id';
    public $incrementing = false;

    protected $fillable = [
        'landing_page_id',
        'region_id',
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

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
