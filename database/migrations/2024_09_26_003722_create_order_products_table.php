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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Size::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Color::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Attribute::class)->nullable()->constrained('attributes')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('price_per_unit', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total_without_fees', 10, 2);
            $table->decimal('total_with_fees', 10, 2);
            $table->string('notes')->nullable();
            $table->enum('status', ['Active', 'Canceled'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
