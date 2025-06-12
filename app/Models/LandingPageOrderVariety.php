<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageOrderVariety extends Model
{
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(LandingPageOrder::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
