<?php

namespace App\Models;

use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Coupon extends Model
{
    use HasFactory;

    use HasTranslations;

    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'expires_at',
        'min_order_amount',
        'is_active',
        'usage_limit',
        'usage_limit_per_user'
    ];

    protected $casts = [
        'value' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function wheelPrizes()
    {
        return $this->hasMany(WheelPrize::class);
    }
}
