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
            $table->text('nav_bar_items_not_active_text_color')->nullable();
            $table->text('nav_bar_items_text_color')->nullable();
            $table->text('nav_bar_background_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn('nav_bar_items_not_active_text_color');
            $table->dropColumn('nav_bar_items_text_color');
            $table->dropColumn('nav_bar_background_color');
        });
    }
};
