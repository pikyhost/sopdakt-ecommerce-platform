<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => OrderStatus::class
    ];

    protected static function booted()
    {
        static::created(function (Order $order) {
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    // Find the product
                    $product = Product::find($item->product_id);

                    if ($product) {
                        // Deduct quantity from product
                        $product->decrement('quantity', $item->quantity);

                        // Deduct quantity from inventory
                        $product->inventory()->decrement('quantity', $item->quantity);

                        Transaction::create([
                            'product_id' => $item->product_id,
                            'type'       => TransactionType::SALE, // Enum type
                            'quantity'   => $item->quantity,
                            'notes'      => "Sale of {$item->quantity} units for Order #{$order->id}",
                        ]);
                    }
                }
            }
        });

        static::deleting(function (Order $order) {
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);

                    if ($product) {
                        // ✅ Restore stock
                        $product->increment('quantity', $item->quantity);
                        $product->inventory()->increment('quantity', $item->quantity);

                        // ✅ Create a restock transaction
                        Transaction::create([
                            'product_id' => $item->product_id,
                            'type'       => TransactionType::RESTOCK,
                            'quantity'   => $item->quantity,
                            'notes'      => "Restock of {$item->quantity} units due to order #{$order->id} deletion.",
                        ]);
                    }
                }
            }
        });
    }

    public function setStatusAttribute($value)
    {
        // If order is being cancelled or refunded, restore stock
        if (in_array($this->status, ['pending', 'preparing', 'shipping']) &&
            in_array($value, ['cancelled', 'refund'])) {

            foreach ($this->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);

                    if ($product) {
                        // ✅ Restore stock
                        $product->increment('quantity', $item->quantity);
                        $product->inventory()->increment('quantity', $item->quantity);

                        // ✅ Create a restock transaction
                        Transaction::create([
                            'product_id' => $item->product_id,
                            'type'       => TransactionType::RESTOCK,
                            'quantity'   => $item->quantity,
                            'notes'      => "Restock of {$item->quantity} units due to order #{$this->id} cancellation.",
                        ]);
                    }
                }
            }
        }

        // ✅ Set the order status
        $this->attributes['status'] = $value;
    }

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
