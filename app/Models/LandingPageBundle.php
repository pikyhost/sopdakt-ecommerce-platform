<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageBundle extends Model
{
    protected $fillable = [
        'id',
        'landing_page_id',
        'name',
        'quantity',
        'price',
        'status',
        'created_at',
        'updated_at',
    ];
}
