<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "10% Off Coupon"
            $table->string('code')->unique(); // Unique coupon code, e.g., "DISC10-XYZ123"
            $table->enum('type', ['free_shipping', 'discount_percentage', 'discount_amount']); // Coupon type
            $table->decimal('value', 8, 2)->nullable(); // e.g., 10.00 for 10% or $10
            $table->dateTime('expires_at')->nullable(); // Coupon expiration
            $table->decimal('min_order_amount', 8, 2)->nullable(); // Minimum order amount
            $table->boolean('is_active')->default(true); // Enable/disable coupon
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
