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
        Schema::create('product_governorate', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Governorate::class)->constrained()->cascadeOnDelete();
            $table->decimal('shipping_cost')->nullable();
            $table->primary(['product_id','governorate_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_governorate');
    }
};
