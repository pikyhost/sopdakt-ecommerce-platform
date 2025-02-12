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
        Schema::table('website_settings', function (Blueprint $table) {
            $table->text('features_section_cta_button_background_color')->nullable()->default(null);
            $table->text('features_section_cta_button_hover_background_color')->nullable()->default(null);
            $table->text('features_section_cta_button_border_color')->nullable()->default(null);
            $table->text('features_section_cta_button_text_hover_color')->nullable()->default(null);
            $table->text('features_section_cta_button_text_color')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn('features_section_cta_button_background_color');
            $table->dropColumn('features_section_cta_button_hover_background_color');
            $table->dropColumn('features_section_cta_button_border_color');
            $table->dropColumn('features_section_cta_button_text_hover_color');
            $table->dropColumn('features_section_cta_button_text_color');
        });
    }
};
