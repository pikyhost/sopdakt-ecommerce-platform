<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_id',
        'shipping_type_id',
        'payment_method_id',
        'coupon_id',
        'shipping_cost',
        'tax_percentage',
        'tax_amount',
        'subtotal',
        'total',
        'status',
        'notes',
        'shipping_type_id',
        'country_id',
        'city_id',
        'governorate_id',
        'tracking_number', 'shipping_status', 'shipping_response'
    ];

    protected $casts = [
        'status' => OrderStatus::class
    ];

    /**
     * Get the user who placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the contact details if the order was placed as a guest.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the shipping type for the order.
     */
    public function shippingType(): BelongsTo
    {
        return $this->belongsTo(ShippingType::class);
    }

    /**
     * Get the payment method used for the order.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the applied coupon, if any.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope to filter orders by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if the order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
