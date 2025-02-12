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
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->tinyInteger('is_feature1_cta_button')->nullable()->default(0);
            $table->string('feature1_cta_button_text')->nullable();
            $table->string('feature1_cta_button_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn('is_feature1_cta_button');
            $table->dropColumn('feature1_cta_button_text');
            $table->dropColumn('feature1_cta_button_link');
        });
    }
};
