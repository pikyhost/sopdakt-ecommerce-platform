<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WheelSpin extends Model
{
    use HasFactory;

    protected $fillable = [
        'wheel_id',
        'user_id',
        'session_id',
        'phone',
        'prize_id',
        'ip_address',
        'next_spin_at',
    ];

    protected $casts = [
        'next_spin_at' => 'datetime',
    ];

    public function wheel()
    {
        return $this->belongsTo(Wheel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function prize()
    {
        return $this->belongsTo(WheelPrize::class);
    }
}
