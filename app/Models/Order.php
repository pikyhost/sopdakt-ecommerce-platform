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
        'status' => OrderStatus::class,
    ];

    protected static function booted()
    {
        // ❌ Prevent duplicate order creation
        static::creating(function (Order $order) {
            $query = Order::query()
                ->where('created_at', '>=', now()->subMinutes(2));

            if ($order->user_id) {
                $query->where('user_id', $order->user_id);
            } elseif ($order->contact_id) {
                $query->where('contact_id', $order->contact_id);
            }

            if ($query->exists()) {
                return false; // silently stop creation
            }
        });

        // ✅ Handle stock deduction after creation
        static::created(function (Order $order) {
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);

                    if ($product) {
                        $product->decrement('quantity', $item->quantity);
                        $product->inventory()->decrement('quantity', $item->quantity);

                        Transaction::create([
                            'product_id' => $item->product_id,
                            'type'       => TransactionType::SALE,
                            'quantity'   => $item->quantity,
                            'notes'      => "Sale of {$item->quantity} units for Order #{$order->id}",
                        ]);
                    }
                }
            }
        });

        // ✅ Restore stock on order deletion
        static::deleting(function (Order $order) {
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);

                    if ($product) {
                        $product->increment('quantity', $item->quantity);
                        $product->inventory()->increment('quantity', $item->quantity);

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
        // Restore stock on cancellation or refund
        if (in_array($this->status, ['pending', 'preparing', 'shipping']) &&
            in_array($value, ['cancelled', 'refund'])) {

            foreach ($this->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);

                    if ($product) {
                        $product->increment('quantity', $item->quantity);
                        $product->inventory()->increment('quantity', $item->quantity);

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

        $this->attributes['status'] = $value;
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function shippingType(): BelongsTo
    {
        return $this->belongsTo(ShippingType::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
