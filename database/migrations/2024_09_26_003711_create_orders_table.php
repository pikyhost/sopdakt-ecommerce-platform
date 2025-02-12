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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Address::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\ShippingType::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\PaymentMethod::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Coupon::class)->nullable()->constrained()->cascadeOnDelete();
            $table->integer('shipping_cost');
            $table->integer('tax');
            $table->integer('total_without_fees');
            $table->integer('total_with_fees');
            $table->enum('status', ['Active','Canceled'])->default('Active');
            $table->enum('shipping_status', ['Preparing', 'Shipping', 'Delivered'])->default('Preparing');
            $table->enum('payment_status', ['Pending', 'Paid', 'Failed'])->default('Pending');
            $table->string('payment_gateway')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
