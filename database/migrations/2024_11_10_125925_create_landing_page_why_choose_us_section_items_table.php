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
        Schema::create('landing_page_why_choose_us_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('landing_pages')->cascadeOnDelete();
            $table->string('title');
            $table->string('image');
            $table->text('background_color');
            $table->text('text_color');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_why_choose_us_section_items');
    }
};
