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
        Schema::table('landing_page_products_section_items', function (Blueprint $table) {
            $table->decimal('after_discount_price', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_page_products_section_items', function (Blueprint $table) {
            $table->decimal('after_discount_price', 10, 2)->nullable(false)->change();
        });
    }
};
