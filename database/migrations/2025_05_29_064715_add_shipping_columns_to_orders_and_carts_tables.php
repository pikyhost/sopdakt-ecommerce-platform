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
        Schema::table('orders_and_carts_tables', function (Blueprint $table) {
            Schema::table('carts', function (Blueprint $table) {
                $table->boolean('is_free_shipping')->default(false);
            });

            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('is_free_shipping')->default(false);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['is_free_shipping', 'shipping_cost']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_free_shipping', 'shipping_cost']);
        });
    }
};
