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
        Schema::create('shipping_type_governorates', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\ShippingType::class);
            $table->foreignIdFor(\App\Models\Governorate::class);
            $table->primary(['shipping_type_id', 'governorate_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_type_governorates');
    }
};
