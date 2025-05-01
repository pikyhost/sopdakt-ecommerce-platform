<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class WheelPrize extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public $translatable = ['name']; // Define translatable fields

    protected $casts = [
        'is_available' => 'boolean',
        'probability' => 'integer',
        'value' => 'integer',
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
}
