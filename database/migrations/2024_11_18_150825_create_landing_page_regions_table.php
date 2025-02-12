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
        Schema::create('landing_page_regions', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\LandingPage::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Region::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\ShippingType::class)->constrained()->cascadeOnDelete();
            $table->decimal('shipping_cost')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->primary(['landing_page_id', 'region_id', 'shipping_type_id'], 'primary_landing_page_region_shipping_type');
            $table->unique(['landing_page_id', 'region_id', 'shipping_type_id'], 'unique_landing_page_region_shipping_type');
            $table->index(['landing_page_id', 'region_id', 'shipping_type_id'], 'index_landing_page_region_shipping_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_regions');
    }
};
