<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_shipping_type', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\ShippingType::class)->constrained()->cascadeOnDelete();
            $table->decimal('shipping_cost')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->primary(['product_id', 'shipping_type_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_shipping_type');
    }
};
