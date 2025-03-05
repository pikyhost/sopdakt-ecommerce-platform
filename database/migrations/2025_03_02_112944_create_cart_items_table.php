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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Product::class)->nullable()->constrained()->cascadeOnDelete(); // Product or bundle
            $table->foreignIdFor(\App\Models\Bundle::class)->nullable()->constrained()->cascadeOnDelete(); // If it's a bundle
            $table->foreignIdFor(\App\Models\Size::class)->nullable()->constrained()->cascadeOnDelete(); // Selected size
            $table->foreignIdFor(\App\Models\Color::class)->nullable()->constrained()->cascadeOnDelete(); // Selected color (defaults to primary)
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('quantity')->default(1); // Quantity of product or bundle
            $table->unsignedInteger('price_per_unit'); // Price per item/bundle
            $table->unsignedInteger('subtotal'); // quantity * price_per_unit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
