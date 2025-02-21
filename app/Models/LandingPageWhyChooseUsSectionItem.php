<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageWhyChooseUsSectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'landing_page_id',
        'title',
        'image',
        'background_color',
        'text_color',
        'created_at',
        'updated_at',
    ];
}
