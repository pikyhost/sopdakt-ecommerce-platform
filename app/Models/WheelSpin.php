<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WheelSpin extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_winner' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function wheel(): BelongsTo
    {
        return $this->belongsTo(Wheel::class);
    }

    public function wheelPrize(): BelongsTo
    {
        return $this->belongsTo(WheelPrize::class);
    }
}
