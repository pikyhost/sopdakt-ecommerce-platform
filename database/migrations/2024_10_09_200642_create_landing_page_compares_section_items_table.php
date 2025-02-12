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
        Schema::create('landing_page_compares_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\LandingPage::class)->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('subtitle');
            $table->string('image');
            $table->tinyInteger('status')->default(1);
            $table->integer('order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_compares_section_items');
    }
};
