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
        Schema::table('landing_page_top_bars', function (Blueprint $table) {
            $table->string('link')->nullable()->change();
            $table->string('title')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_page_top_bars', function (Blueprint $table) {
            $table->string('link')->nullable(false)->change();
            $table->string('title')->nullable(false)->change();
        });
    }
};
