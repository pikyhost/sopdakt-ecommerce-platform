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
            $table->tinyInteger('is_features2')->default(0);
            $table->string('feature2_title')->nullable();
            $table->string('feature2_subtitle')->nullable();
            $table->string('feature2_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn('is_features2');
            $table->dropColumn('feature2_title');
            $table->dropColumn('feature2_subtitle');
            $table->dropColumn('feature2_image');
        });
    }
};
