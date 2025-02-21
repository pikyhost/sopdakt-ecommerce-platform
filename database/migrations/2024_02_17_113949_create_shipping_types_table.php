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
        Schema::create('shipping_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Express, Standard, etc.
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('shipping_cost')->default(0);
            $table->string('shipping_estimate_time')->default('0-0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_types');
    }
};
