<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WheelSpin extends Model
{
    protected $fillable = [
        'user_id',
        'wheel_id',
        'wheel_prize_id',
        'is_winner',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wheel(): BelongsTo
    {
        return $this->belongsTo(Wheel::class);
    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(WheelPrize::class, 'wheel_prize_id');
    }
}
