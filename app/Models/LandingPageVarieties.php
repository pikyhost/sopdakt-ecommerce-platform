<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageVarieties extends Model
{
    use HasFactory;

    function color(){
        return $this->belongsTo(Color::class);
    }

    function size(){
        return $this->belongsTo(Size::class);
    }
}
