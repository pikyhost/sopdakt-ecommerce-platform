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
        Schema::table('shipping_types', function (Blueprint $table) {
            $table->dropColumn('shipping_cost');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_types', function (Blueprint $table) {
            $table->decimal('shipping_cost')->default(0);
        });
    }
};
