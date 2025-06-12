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
        Schema::table('landing_page_settings', function (Blueprint $table) {
            $table->string('product_criteria_title_color')->nullable();
            $table->string('product_criteria_price_color')->nullable();
            $table->string('product_criteria_price_after_discount_color')->nullable();
            $table->string('product_criteria_description_color')->nullable();
            $table->string('product_criteria_sku_label_color')->nullable();
            $table->string('product_criteria_sku_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_page_settings', function (Blueprint $table) {
            //
        });
    }
};
