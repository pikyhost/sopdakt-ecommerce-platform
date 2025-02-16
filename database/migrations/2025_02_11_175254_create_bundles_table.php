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
        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Bundle name
            $table->string('name_for_admin')->nullable();
            $table->foreignId('main_product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->enum('bundle_type', ['fixed_price', 'discount_percentage', 'buy_x_get_y'])->nullable(); // Bundle type for discount bundles
            $table->integer('discount_price')->nullable(); // Fixed bundle price
            $table->unsignedInteger('buy_x')->nullable(); // Needed for "Buy X Get Y Free"
            $table->unsignedInteger('get_y')->nullable(); // Needed for "Buy X Get Y Free"
            $table->decimal('discount_percentage', 5, 2)->nullable(); // Discount %
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundles');
    }
};
