<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupon extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'discount_id',
        'code',
        'usage_limit_per_user',
        'total_usage_limit',
    ];

    /**
     * Get the discount associated with the coupon.
     *
     * @return BelongsTo
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Check if the coupon has reached its total usage limit.
     *
     * @return bool
     */
    public function hasReachedTotalUsageLimit(): bool
    {
        if (is_null($this->total_usage_limit)) {
            return false;
        }

        // You'll need to implement a way to track actual usage
        // For example, you might have a coupon_usage table
        $usageCount = 0; // Get actual usage count from your database

        return $usageCount >= $this->total_usage_limit;
    }

    /**
     * Check if the coupon has reached its per-user usage limit for a given user.
     *
     * @param  mixed  $user
     * @return bool
     */
    public function hasReachedPerUserUsageLimit($user): bool
    {
        if (is_null($this->usage_limit_per_user)) {
            return false;
        }

        // Get actual usage count for this user from your database
        $userUsageCount = 0;

        return $userUsageCount >= $this->usage_limit_per_user;
    }
}
