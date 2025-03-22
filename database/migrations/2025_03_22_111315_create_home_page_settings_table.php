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
        Schema::create('home_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('main_heading')->default('Spring / Summer Season');
            $table->string('discount_text')->default('Up to');
            $table->string('discount_value')->default('50% off');
            $table->decimal('starting_price', 8, 2)->default(19.99);
            $table->string('currency_symbol')->default('$');
            $table->string('button_text')->default('Shop Now');
            $table->string('button_url')->default('#');
            $table->string('background_image')->nullable();
            $table->string('layer_image')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_page_settings');
    }
};
