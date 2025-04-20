<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wheel extends Model
{
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'delay_seconds' => 'integer',
        'specific_pages' => 'array', // Cast JSON string to array
    ];

    public function prizes()
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
