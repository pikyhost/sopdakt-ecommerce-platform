<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Wheel extends Model
{
    use HasFactory;

    use HasTranslations;

    public $translatable = ['name' ,'description'];

    protected $casts = [
        'specific_pages' => 'array',
        'is_active' => 'boolean',
        'require_phone' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'description',
        'daily_spin_limit',
        'time_between_spins_minutes',
        'require_phone',
        'is_active',
        'display_rules',
        'popup_order',
        'show_interval_minutes',
        'delay_seconds',
        'duration_seconds',
        'dont_show_again_days',
        'specific_pages'
    ];

    public function prizes()
    {
        return $this->hasMany(WheelPrize::class);
    }

    public function spins()
    {
        return $this->hasMany(WheelSpin::class);
    }

    public function preferences()
    {
        return $this->hasMany(WheelPreference::class);
    }

    /**
     * Determine if wheel should be displayed on current page
     */
    public function shouldDisplayOnPage(string $currentPath): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $specificPages = $this->specific_pages ?? [];
        $currentPath = trim($currentPath, '/') ?: '/';

        switch ($this->display_rules) {
            case 'all_pages':
                return true;

            case 'specific_pages':
                return in_array($currentPath, $specificPages);

            case 'all_except_specific':
                return !in_array($currentPath, $specificPages);

            // Add cases for page_group and all_except_group if you implement groups

            default:
                return true;
        }
    }

    /**
     * Check if wheel should be hidden for user/guest
     */
    public function shouldBeHiddenFor($user = null, $sessionId = null): bool
    {
        $query = $this->preferences()
            ->where('hide_until', '>', now());

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->exists();
    }
}
