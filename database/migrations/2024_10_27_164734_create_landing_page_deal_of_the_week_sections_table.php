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
        Schema::create('landing_page_deal_of_the_week_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('image')->nullable();
            $table->string('rate')->nullable();
            $table->tinyInteger('status')->default(1)->nullable();
            $table->integer('order')->default(0)->nullable();
            $table->tinyInteger('cta_button')->default(0)->nullable();
            $table->string('cta_button_text')->nullable();
            $table->string('cta_button_link')->nullable();
            $table->decimal('price', 10, 2)->default(0)->nullable();
            $table->decimal('after_discount_price', 10, 2)->default(0)->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_deal_of_the_week_sections');
    }
};
