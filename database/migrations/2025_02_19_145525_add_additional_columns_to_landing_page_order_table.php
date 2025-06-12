<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('landing_page_orders', function (Blueprint $table) {
            $table->string('tracking_number')->nullable();
            $table->string('shipping_status')->nullable();
            $table->json('shipping_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_page_orders', function (Blueprint $table) {
            $table->dropColumn(['tracking_number', 'shipping_status', 'shipping_response']);
        });
    }
};
