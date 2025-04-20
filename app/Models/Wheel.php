<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wheel extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'start_date',
        'end_date',
        'spins_per_user',
        'spins_duration',
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
