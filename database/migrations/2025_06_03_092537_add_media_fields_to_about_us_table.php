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
        Schema::table('about_us', function (Blueprint $table) {
            // Feature 1
            $table->string('feature_title_one')->nullable();
            $table->string('feature_subtitle_one')->nullable();
            $table->string('feature_image_one')->nullable();

            // Feature 2
            $table->string('feature_title_two')->nullable();
            $table->string('feature_subtitle_two')->nullable();
            $table->string('feature_image_two')->nullable();

            // Feature 3
            $table->string('feature_title_three')->nullable();
            $table->string('feature_subtitle_three')->nullable();
            $table->string('feature_image_three')->nullable();

            // Feature 4
            $table->string('feature_title_four')->nullable();
            $table->string('feature_subtitle_four')->nullable();
            $table->string('feature_image_four')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('about_us', function (Blueprint $table) {
            //
        });
    }
};
