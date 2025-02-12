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
            $table->decimal('subtotal', 8, 2)->after('quantity');
            $table->decimal('total', 8, 2)->after('subtotal');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_page_orders', function (Blueprint $table) {
            $table->dropColumn('subtotal');
            $table->dropColumn('total');
        });
    }
};
