<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WheelPrize extends Model
{
    protected $fillable = [
        'wheel_id',
        'name',
        'type',
        'value',
        'coupon_id',
        'discount_id',
        'probability',
        'is_available',
        'daily_limit',
        'total_limit',
    ];

    public function wheel(): BelongsTo
    {
        return $this->belongsTo(Wheel::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function spins()
    {
        return $this->hasMany(WheelSpin::class);
    }
}
