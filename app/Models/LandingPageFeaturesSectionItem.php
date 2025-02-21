<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageFeaturesSectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'landing_page_id',
        'title',
        'subtitle',
        'image',
        'status',
        'order',
        'created_at',
        'updated_at',
    ];
}
