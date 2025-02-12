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
        Schema::table('landing_page_settings', function (Blueprint $table) {
            $table->string('product_criteria_cta_button_background_color')->nullable();
            $table->string('product_criteria_cta_button_hover_background_color')->nullable();
            $table->string('product_criteria_cta_button_border_color')->nullable();
            $table->string('product_criteria_cta_button_text_hover_color')->nullable();
            $table->string('product_criteria_cta_button_text_color')->nullable();
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
