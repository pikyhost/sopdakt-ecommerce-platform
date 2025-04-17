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
    protected $fillable = [
        'title',
        'description',
        'image_path',
        'cta_text',
        'cta_link',
        'delay_seconds',
        'is_active',
        'display_rules',
        'specific_pages',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'delay_seconds' => 'integer',
        'specific_pages' => 'array', // Cast JSON string to array
    ];

    /**
     * Scope to get only active popups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this popup should be shown on a given route or path.
     */
    public function shouldDisplayOn(string $path): bool
    {
        if ($this->display_rules === 'all_pages') {
            return true;
        }

        if ($this->display_rules === 'specific_pages' && is_array($this->specific_pages)) {
            return in_array($path, $this->specific_pages);
        }

        // Add logic for page_group if needed
        return false;
    }
}
