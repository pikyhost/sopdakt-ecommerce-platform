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
        Schema::table('popups', function (Blueprint $table) {
            $table->enum('display_rules', [
                'all_pages',
                'specific_pages',
                'page_group',
                'all_except_specific',
                'all_except_group'
            ])->default('all_pages')->change();

            $table->unsignedInteger('popup_order')->default(0);
            $table->unsignedInteger('show_interval_minutes')->default(60);
            $table->unsignedInteger('delay_seconds')->default(60);
            $table->unsignedInteger('duration_seconds')->default(60);
            $table->unsignedInteger('dont_show_again_days')->default(7);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            //
        });
    }
};
