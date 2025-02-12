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
        Schema::create('landing_page_products_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\LandingPage::class)->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('subtitle');
            $table->string('image');
            $table->tinyInteger('status')->default(1);
            $table->integer('order')->default(0);
            $table->tinyInteger('cta_button')->default(0);
            $table->string('cta_button_text')->nullable();
            $table->string('cta_button_link')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('after_discount_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_products_section_items');
    }
};
