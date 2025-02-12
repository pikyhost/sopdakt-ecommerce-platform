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
        Schema::create('shipping_type_zones', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\ShippingType::class);
            $table->foreignIdFor(\App\Models\Zone::class);
            $table->primary(['shipping_type_id', 'zone_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_type_zones');
    }
};
