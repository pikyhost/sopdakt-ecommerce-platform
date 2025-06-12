<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageAboutSectionItem extends Model
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
        'is_cta_button',
        'cta_button_text',
        'cta_button_link',
        'created_at',
        'updated_at',
    ];
}
