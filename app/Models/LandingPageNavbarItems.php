<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageNavbarItems extends Model
{
    protected $fillable = [
        'id',
        'name',
        'display_name',
        'status	',
        'created_at',
        'updated_at',
    ];
}
