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
            $table->text('top_bar_background_color')->nullable();
            $table->text('top_bar_text_color')->nullable();

            $table->text('top_bar_dark_background_color')->nullable();
            $table->text('top_bar_dark_text_color')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn('top_bar_background_color');
            $table->dropColumn('top_bar_text_color');

            $table->dropColumn('top_bar_dark_background_color');
            $table->dropColumn('top_bar_dark_text_color');
        });
    }
};
