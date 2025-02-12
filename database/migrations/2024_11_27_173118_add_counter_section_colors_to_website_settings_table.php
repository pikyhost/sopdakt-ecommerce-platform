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
            $table->text('counter_section_background_color')->nullable();
            $table->text('counter_section_counter_color')->nullable();
            $table->text('counter_section_cta_button_color')->nullable();
            $table->text('counter_section_cta_button_text_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_tables', function (Blueprint $table) {
            $table->dropColumn('counter_section_background_color');
            $table->dropColumn('counter_section_counter_color');
            $table->dropColumn('counter_section_cta_button_color');
            $table->dropColumn('counter_section_cta_button_text_color');
        });
    }
};
