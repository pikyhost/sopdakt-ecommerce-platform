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
            $table->foreignIdFor(\App\Models\Country::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\ShippingType::class)->constrained()->cascadeOnDelete();
            $table->decimal('shipping_cost')->nullable();
            $table->tinyInteger('status')->default(1);
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
