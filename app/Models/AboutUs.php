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
        'team_members' => 'array',
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

    public function getTeamMembersAttribute($value)
    {
        $localeValue = $this->getTranslations('team_members')[app()->getLocale()] ?? [];

        // Decode if it's a JSON string
        if (is_string($localeValue)) {
            $decoded = json_decode($localeValue, true);
        } else {
            $decoded = $localeValue;
        }

        if (!is_array($decoded)) {
            return [];
        }

        // If it's already a list (like in Arabic)
        if (array_is_list($decoded)) {
            return $decoded;
        }

        // If it's an associative object (like English version), convert to list
        return collect($decoded)->map(function ($member) {
            return [
                'name' => $member['name'] ?? '',
                'image' => is_array($member['image']) ? Arr::first($member['image']) : $member['image'],
                'position' => $member['position'] ?? null,
            ];
        })->values()->toArray();
    }

    // Mutator for team members to ensure proper JSON
    public function setTeamMembersAttribute($value)
    {
        $this->attributes['team_members'] = json_encode($value);
    }
}
