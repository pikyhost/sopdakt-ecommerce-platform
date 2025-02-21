<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageProductFeature extends Model
{
    protected $fillable = [
        'id',
        'landing_page_id',
        'title',
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }
}
