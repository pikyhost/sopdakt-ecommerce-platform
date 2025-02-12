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
            $table->tinyInteger('is_features1')->default(0);
            $table->string('feature1_title');
            $table->string('feature1_subtitle')->nullable();
            $table->string('feature1_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn('is_features1');
            $table->dropColumn('feature1_title');
            $table->dropColumn('feature1_subtitle');
            $table->dropColumn('feature1_image');
        });
    }
};
