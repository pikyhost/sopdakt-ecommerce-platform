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
        Schema::create('landing_page_order_varieties', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\LandingPageOrder::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Size::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Color::class)->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_order_varieties');
    }
};
