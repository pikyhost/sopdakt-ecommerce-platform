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
        Schema::table('landing_page_orders', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete(); // Assuming a 'cities' table exists
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_page_orders', function (Blueprint $table) {
            //
        });
    }
};
