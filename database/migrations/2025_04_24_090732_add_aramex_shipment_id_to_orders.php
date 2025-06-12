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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('aramex_shipment_id')->nullable()->after('status');
            $table->string('aramex_tracking_number')->nullable()->after('aramex_shipment_id');
            $table->string('aramex_tracking_url')->nullable()->after('aramex_tracking_number');
            $table->text('aramex_response')->nullable()->after('aramex_tracking_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
