<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class Order extends Model
{
    use HasFactory, HasFilamentComments;

    protected $guarded = [];

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    protected static function booted()
    {
        // Prevent duplicate orders
        static::creating(function (Order $order) {
            if (!empty($order->checkout_token) && Order::where('checkout_token', $order->checkout_token)->exists()) {
                throw new \Exception('Duplicate order attempt.');
            }
        });

        // Restore stock if order deleted
        static::deleting(function (Order $order) {
            $order->restoreInventory();
        });
    }

    public function syncInventoryOnCreate()
    {
        foreach ($this->items as $item) {
            if ($item->product_id) {
                $product = Product::find($item->product_id);

                if ($product) {
                    $product->decrement('quantity', $item->quantity);

                    // Decrement variant quantity
                    $variant = $product->productColors()
                        ->where('color_id', $item->color_id)
                        ->first()
                        ?->productColorSizes()
                        ->where('size_id', $item->size_id)
                        ->first();

                    if ($variant) {
                        $variant->decrement('quantity', $item->quantity);
                    }

                    $product->inventory()?->decrement('quantity', $item->quantity);

                    Transaction::create([
                        'product_id' => $item->product_id,
                        'type'       => TransactionType::SALE,
                        'quantity'   => $item->quantity,
                        'notes'      => "Sale of {$item->quantity} units for Order #{$this->id}",
                    ]);
                }
            }
        }
    }

    public function restoreInventory()
    {
        foreach ($this->items as $item) {
            if ($item->product_id) {
                $product = Product::find($item->product_id);

                if ($product) {
                    $product->increment('quantity', $item->quantity);

                    // Restore variant quantity
                    $variant = $product->productColors()
                        ->where('color_id', $item->color_id)
                        ->first()
                        ?->productColorSizes()
                        ->where('size_id', $item->size_id)
                        ->first();

                    if ($variant) {
                        $variant->increment('quantity', $item->quantity);
                    }

                    $product->inventory()?->increment('quantity', $item->quantity);

                    Transaction::create([
                        'product_id' => $item->product_id,
                        'type'       => TransactionType::RESTOCK,
                        'quantity'   => $item->quantity,
                        'notes'      => "Restock of {$item->quantity} units due to Order #{$this->id} status change.",
                    ]);
                }
            }
        }
    }

    // Relationships

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

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

    public function couponUsage()
    {
        return $this->hasOne(CouponUsage::class);
    }

    // Define the uniqueness of the order items for hashing
    public function getOrderItemsHash()
    {
        return sha1(json_encode($this->orderItems->pluck('product_id', 'quantity')->toArray()));
    }

}
