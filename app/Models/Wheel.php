<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Wheel extends Model
{
    use HasFactory, HasTranslations;

    protected $guarded = [];

    public $translatable = ['name']; // Define translatable fields

    protected $casts = [
        'is_active' => 'boolean',
        'specific_pages' => 'array', // Cast JSON string to array
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'spins_per_user' => 'integer',
        'spins_duration' => 'integer',
    ];

    public function prizes(): HasMany
    {
        return $this->hasMany(WheelPrize::class);
    }

    public function wheelPrizes(): HasMany
    {
        return $this->hasMany(WheelPrize::class);
    }

    public function spins(): HasMany
    {
        return $this->hasMany(WheelSpin::class);
    }

    public function isActive(): bool
    {
        return $this->is_active &&
            (is_null($this->start_date) || now()->gte($this->start_date)) &&
            (is_null($this->end_date) || now()->lte($this->end_date));
    }
}
