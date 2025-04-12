<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AboutUs extends Model
{
    use HasTranslations;

    protected $table = 'about_us';

    protected $guarded = [];

    protected $casts = [
        'team_members' => 'array', // Automatically cast JSON to array
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
        'team_members', // Each name in the array will be translatable
        'testimonial_content',
        'testimonial_name',
        'testimonial_role',
        'meta_title',
        'meta_description',
    ];

    // Accessor for team members to ensure array
    public function getTeamMembersAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    // Mutator for team members to ensure proper JSON
    public function setTeamMembersAttribute($value)
    {
        $this->attributes['team_members'] = json_encode($value);
    }
}
