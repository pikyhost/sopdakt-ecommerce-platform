<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Add columns to carts table
        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('coupon_id')
                ->nullable()
                ->constrained('coupons')
                ->nullOnDelete();

            $table->unsignedInteger('discount_amount')
                ->default(0)
                ->comment('The total discount amount applied to the cart');
        });

        // Add columns to coupons table
        Schema::table('coupons', function (Blueprint $table) {
            $table->integer('used_count')
                ->default(0)
                ->comment('How many times this coupon has been used');
        });

        // Add columns to discounts table
        Schema::table('discounts', function (Blueprint $table) {
            $table->integer('used_count')
                ->default(0)
                ->comment('How many times this discount has been used through all coupons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'discount_amount']);
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('used_count');
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->dropColumn('used_count');
        });
    }
};
