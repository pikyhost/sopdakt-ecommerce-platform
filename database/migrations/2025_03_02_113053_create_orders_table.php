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
            $table->foreignIdFor(\App\Models\User::class)->nullable()->constrained()->cascadeOnDelete(); // Authenticated users
            $table->foreignIdFor(\App\Models\Contact::class)->nullable()->constrained()->cascadeOnDelete(); // Shipping contact info
            $table->foreignIdFor(\App\Models\PaymentMethod::class)->constrained()->cascadeOnDelete(); // Payment method
            $table->foreignIdFor(\App\Models\Coupon::class)->nullable()->constrained()->cascadeOnDelete(); // Applied discount
            $table->unsignedInteger('shipping_cost')->nullable(); // Optional, free shipping cases
            $table->unsignedTinyInteger('tax_percentage')->default(0);
            $table->integer('tax_amount')->default(0);
            $table->unsignedInteger('subtotal')->default(0); // Sum of all cart items' subtotals
            $table->unsignedInteger('total')->default(0); // Final total after apply
            $table->foreignId('shipping_type_id')->nullable()->constrained('shipping_types')->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->cascadeOnDelete();
            $table->foreignId('governorate_id')->nullable()->constrained('governorates')->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->cascadeOnDelete();
            $table->enum('status', ['pending', 'preparing', 'shipping', 'delayed', 'refund', 'cancelled', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->string('tracking_number')->nullable();
            $table->string('shipping_status')->nullable();
            $table->json('shipping_response')->nullable();

            $table->uuid('checkout_token')->unique()->nullable();
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
