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
        Schema::create('product_special_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // Can apply to a country or a group of countries
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('country_group_id')->nullable()->constrained('country_groups')->onDelete('cascade');

            $table->integer('special_price');
            $table->integer('special_price_after_discount')->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_special_prices');
    }
};
