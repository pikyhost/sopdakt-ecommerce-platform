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
            $table->unsignedInteger('delay_seconds')->default(60);  // Delay in seconds before showing popup
            $table->unsignedInteger('duration_seconds')->default(60);  // Delay in seconds before showing popup
            $table->unsignedInteger('dont_show_again_days')->default(7);  // Delay in seconds before showing popup

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
