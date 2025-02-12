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
        Schema::table('website_settings', function (Blueprint $table) {
            $table->string('landing_page_primary_background_color')->nullable();
            $table->string('landing_page_primary_text_color')->nullable();
            $table->string('landing_page_primary_highlight_color')->nullable();
            $table->string('landing_page_primary_accent_color')->nullable();
            $table->string('landing_page_secondary_text_color')->nullable();
            $table->string('landing_page_button_accent_background')->nullable();
            $table->string('landing_page_button_accent_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn('landing_page_primary_background_color');
            $table->dropColumn('landing_page_primary_text_color');
            $table->dropColumn('landing_page_primary_highlight_color');
            $table->dropColumn('landing_page_primary_accent_color');
            $table->dropColumn('landing_page_secondary_text_color');
            $table->dropColumn('landing_page_button_accent_background');
            $table->dropColumn('landing_page_button_accent_color');
        });
    }
};
