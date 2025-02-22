<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageSetting extends Model
{
    protected $fillable = [
        'id',
        'landing_page_header_image',
        'created_at',
        'updated_at',
    ];
}
