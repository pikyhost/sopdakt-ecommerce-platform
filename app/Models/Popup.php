<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Popup extends Model
{
    use HasTranslations;

    /**
     * Translatable attributes.
     */
    public $translatable = [
        'title',
        'description',
        'cta_text',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'delay_seconds' => 'integer',
        'specific_pages' => 'array',
    ];

    /**
     * Scope to get only active popups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
