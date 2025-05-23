<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DiscountsAndCouponsSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        Discount::truncate();
        Coupon::truncate();

        // Create discounts
        $discounts = [
            [
                'name' => 'Summer Sale',
                'description' => 'Get 20% off on all products',
                'discount_type' => 'percentage',
                'applies_to' => 'product',
                'value' => 20,
                'min_order_value' => null,
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
                'usage_limit' => 100,
                'requires_coupon' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Free Shipping',
                'description' => 'Free shipping on orders over $50',
                'discount_type' => 'free_shipping',
                'applies_to' => 'cart',
                'value' => null,
                'min_order_value' => 50,
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
                'usage_limit' => null,
                'requires_coupon' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Fixed Discount',
                'description' => '$10 off your order',
                'discount_type' => 'fixed',
                'applies_to' => 'cart',
                'value' => 10,
                'min_order_value' => 30,
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
                'usage_limit' => 50,
                'requires_coupon' => true,
                'is_active' => true,
            ],
        ];

        foreach ($discounts as $discountData) {
            $discount = Discount::create($discountData);

            // Create associated coupons
            switch ($discount->name) {
                case 'Summer Sale':
                    Coupon::create([
                        'discount_id' => $discount->id,
                        'code' => 'SUMMER20',
                        'usage_limit_per_user' => 1,
                        'total_usage_limit' => 100,
                        'is_active' => true,
                    ]);
                    break;

                case 'Free Shipping':
                    Coupon::create([
                        'discount_id' => $discount->id,
                        'code' => 'FREESHIP',
                        'usage_limit_per_user' => 2,
                        'total_usage_limit' => null,
                        'is_active' => true,
                    ]);
                    break;

                case 'Fixed Discount':
                    Coupon::create([
                        'discount_id' => $discount->id,
                        'code' => 'SAVE10',
                        'usage_limit_per_user' => 1,
                        'total_usage_limit' => 50,
                        'is_active' => true,
                    ]);
                    break;
            }
        }

        // Attach products to product-specific discounts
        $productDiscount = Discount::where('name', 'Summer Sale')->first();
        $products = Product::take(3)->get();
        $productDiscount->products()->attach($products);
    }
}
