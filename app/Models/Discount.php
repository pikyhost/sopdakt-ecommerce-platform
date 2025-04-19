<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    use HasTranslations;

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = ['name', 'description'];

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
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'requires_coupon' => 'boolean',
        'price' => 'integer',
        'after_discount_price' => 'integer',
    ];

    /**
     * Available discount types.
     *
     * @var array
     */
    public const DISCOUNT_TYPES = [
        'percentage' => 'Percentage',
        'fixed' => 'Fixed Amount',
        'free_shipping' => 'Free Shipping',
    ];

    /**
     * Available application scopes.
     *
     * @var array
     */
    public const APPLIES_TO = [
        'product' => 'Product',
        'category' => 'Category',
        'cart' => 'Cart',
        'collection' => 'Collection',
    ];

    /**
     * Get the products associated with the discount.
     *
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'discount_product');
    }

    /**
     * Get the categories associated with the discount.
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'discount_category');
    }

    /**
     * Get the collections associated with the discount.
     *
     * @return BelongsToMany
     */
    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_discount');
    }


    /**
     * Get the coupons for this discount.
     *
     * @return HasMany
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Calculate the after discount price.
     *
     * @return int|null
     */
    public function calculateAfterDiscountPrice(): ?int
    {
        if ($this->discount_type === 'percentage') {
            return $this->price - ($this->price * ($this->value / 100));
        }

        if ($this->discount_type === 'fixed') {
            return $this->price - $this->value;
        }

        return null;
    }

    /**
     * Check if the discount is currently active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $now = now();

        return ($this->starts_at === null || $this->starts_at <= $now) &&
            ($this->ends_at === null || $this->ends_at >= $now);
    }

    /**
     * Scope a query to only include active discounts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        $now = now();

        return $query->where(function($q) use ($now) {
            $q->whereNull('starts_at')
                ->orWhere('starts_at', '<=', $now);
        })->where(function($q) use ($now) {
            $q->whereNull('ends_at')
                ->orWhere('ends_at', '>=', $now);
        });
    }

    /**
     * Scope a query to only include discounts that require a coupon.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresCoupon($query)
    {
        return $query->where('requires_coupon', true);
    }

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($discount) {
            if (in_array($discount->discount_type, ['percentage', 'fixed'])) {
                $discount->after_discount_price = $discount->calculateAfterDiscountPrice();
            } else {
                $discount->after_discount_price = null;
            }
        });
    }
}
