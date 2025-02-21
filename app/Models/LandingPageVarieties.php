<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageVarieties extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'landing_page_id',
        'size_id',
        'color_id',
        'price',
        'quantity',
        'created_at',
        'updated_at',
    ];

    function color()
    {
        return $this->belongsTo(Color::class);
    }

    function size()
    {
        return $this->belongsTo(Size::class);
    }
}
