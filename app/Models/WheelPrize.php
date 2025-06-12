<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class WheelPrize extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'wheel_id',
        'name',
        'coupon_id',
        'probability',
        'is_active',
    ];

    public $translatable = ['name'];

    protected $casts = [
        'probability' => 'integer',
        'is_active' => 'boolean',
    ];

    public function wheel()
    {
        return $this->belongsTo(Wheel::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class)->withDefault();
    }

    public function spins()
    {
        return $this->hasMany(WheelSpin::class, 'prize_id');
    }
}
