<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'product_id',
        'original_price',
        'discounted_price',
        'starts_at',
        'ends_at',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship to the product this coupon applies to
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship to cart items that used this coupon
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'coupon_id');
    }

    /**
     * Calculate discount percentage
     */
    public function getDiscountPercentageAttribute(): float
    {
        return round((($this->original_price - $this->discounted_price) / $this->original_price) * 100, 2);
    }
