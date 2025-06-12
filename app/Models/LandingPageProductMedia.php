<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageProductMedia extends Model
{
    protected $fillable = ['url', 'type'];

    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }
}
