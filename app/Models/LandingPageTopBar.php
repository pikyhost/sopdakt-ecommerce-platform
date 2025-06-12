<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageTopBar extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'landing_page_id',
        'title',
        'link',
        'created_at',
        'updated_at',
    ];
}
