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
            $table->tinyInteger('is_deal_of_the_week')->default(0);
            $table->string('deal_of_the_week_title')->nullable();
            $table->string('deal_of_the_week_subtitle')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn('is_deal_of_the_week');
            $table->dropColumn('deal_of_the_week_title');
            $table->dropColumn('deal_of_the_week_subtitle');
        });
    }
};
