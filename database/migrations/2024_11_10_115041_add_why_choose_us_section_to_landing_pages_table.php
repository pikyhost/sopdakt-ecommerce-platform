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

            $table->tinyInteger('is_why_choose_us')->default(0);
            $table->string('why_choose_us_title');
            $table->string('why_choose_us_subtitle')->nullable();
            $table->string('why_choose_us_video')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            //
        });
    }
};
