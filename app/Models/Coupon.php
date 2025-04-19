<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Coupon extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'usage_limit_per_user' => 'integer',
        'total_usage_limit' => 'integer',
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
     * Get all usages of this coupon.
     *
     * @return HasMany
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Check if the coupon is valid (not expired and within usage limits).
     *
     * @return bool
     */
    public function isValid(): bool
    {
        // Check if coupon's discount is active
        if (!$this->discount->isActive()) {
            return false;
        }

        // Check total usage limit
        if ($this->hasReachedTotalUsageLimit()) {
            return false;
        }

        return true;
    }

    /**
     * Check if the coupon is valid for a specific user.
     *
     * @param  int|string  $userId
     * @return bool
     */
    public function isValidForUser($userId): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check per-user usage limit
        if ($this->hasReachedPerUserUsageLimit($userId)) {
            return false;
        }

        return true;
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

        return $this->usages()->count() >= $this->total_usage_limit;
    }

    /**
     * Check if the coupon has reached its per-user usage limit.
     *
     * @param  int|string  $userId
     * @return bool
     */
    public function hasReachedPerUserUsageLimit($userId): bool
    {
        if (is_null($this->usage_limit_per_user)) {
            return false;
        }

        return $this->usages()
                ->where('user_id', $userId)
                ->count() >= $this->usage_limit_per_user;
    }

    /**
     * Record a usage of this coupon.
     *
     * @param  int|string  $userId
     * @param  int|null  $orderId
     * @return CouponUsage
     */
    public function recordUsage($userId, $orderId = null): CouponUsage
    {
        return $this->usages()->create([
            'user_id' => $userId,
            'order_id' => $orderId,
        ]);
    }

    /**
     * Scope a query to only include active coupons (with active discounts).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereHas('discount', function ($q) {
            $q->active();
        });
    }

    /**
     * Find a coupon by its code.
     *
     * @param  string  $code
     * @return Coupon|null
     */
    public static function findByCode(string $code): ?Coupon
    {
        return static::where('code', strtoupper($code))->first();
    }

    /**
     * Generate a random coupon code.
     *
     * @param  int  $length
     * @param  string  $prefix
     * @return string
     */
    public static function generateCode(int $length = 8, string $prefix = ''): string
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = $prefix;

        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[rand(0, strlen($chars) - 1)];
        }

        // Ensure the code is unique
        while (static::where('code', $code)->exists()) {
            $code = $prefix;
            for ($i = 0; $i < $length; $i++) {
                $code .= $chars[rand(0, strlen($chars) - 1)];
            }
        }

        return $code;
    }
}
