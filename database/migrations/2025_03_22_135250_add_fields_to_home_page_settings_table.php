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
        Schema::table('home_page_settings', function (Blueprint $table) {
            $table->string('center_main_heading')->default('Discover Our Collection');
            $table->string('center_button_text')->default('Explore Now');
            $table->string('center_button_url')->default('#');

            $table->string('last1_heading')->default('Summer Sale');
            $table->string('last1_subheading')->default('Get 20% Off');
            $table->string('last1_button_text')->default('Shop Now');
            $table->string('last1_button_url')->default('#');

            $table->string('last2_heading')->default('Flash Sale');
            $table->string('last2_subheading')->default('Get 30% Off');
            $table->string('last2_button_text')->default('Shop Now');
            $table->string('last2_button_url')->default('#');

            $table->string('latest_heading')->default('Explore the Best of You');
            $table->string('latest_button_text')->default('Shop Now');
            $table->string('latest_button_url')->default('#');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table) {
            //
        });
    }
};
