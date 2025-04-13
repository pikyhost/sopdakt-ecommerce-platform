<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Spatie\Translatable\HasTranslations;

class AboutUs extends Model
{
    use HasTranslations;

    protected $table = 'about_us';

    protected $guarded = [];

    protected $casts = [
        'team_members' => 'array', // English default
        'team_members_ar' => 'array', // Add it at new migration
    ];

    public $translatable = [
        'about_title',
        'team_title',
        'testimonial_title',
        'header_title',
        'header_subtitle',
        'breadcrumb_home',
        'breadcrumb_current',
        'about_description_1',
        'about_description_2',
        'accordion_title_1',
        'accordion_content_1',
        'accordion_title_2',
        'accordion_content_2',
        'accordion_title_3',
        'accordion_content_3',
        'accordion_title_4',
        'accordion_content_4',
        'testimonial_content',
        'testimonial_name',
        'testimonial_role',
        'meta_title',
        'meta_description',
        'cta_text',
    ];
}
