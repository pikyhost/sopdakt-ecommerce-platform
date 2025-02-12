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
        Schema::create('shipping_type_regions', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\ShippingType::class);
            $table->foreignIdFor(\App\Models\Region::class);
            $table->primary(['shipping_type_id', 'region_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_type_regions');
    }
};
