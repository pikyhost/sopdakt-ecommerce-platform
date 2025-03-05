<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('payment_methods')->updateOrInsert(
            ['name' => 'Cash on Delivery'],
            [
                'description' => 'Pay with cash upon delivery',
                'image' => null,
                'is_active' => true,
                'sort_order' => 1,
                'driver' => 'App\Payments\CashOnDelivery',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
