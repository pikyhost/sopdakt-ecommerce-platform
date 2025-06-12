<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageProductsSectionItem extends Model
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
        'cta_button',
        'cta_button_text',
        'cta_button_link',
        'price',
        'after_discount_price',
        'end_date',
        'created_at',
        'updated_at',
    ];
}
