<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageComparesSectionItem extends Model
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
        'price',
        'brand',
        'color',
        'dimensions',
        'weight',
        'attributes',
        'cta_button',
        'cta_button_text',
        'cta_button_link',
        'created_at',
        'updated_at',
    ];

    function criteria()
    {
        return $this->hasMany(LandingPageComparesSectionItemCriteria::class);
    }
}
