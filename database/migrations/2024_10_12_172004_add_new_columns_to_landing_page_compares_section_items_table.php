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
        Schema::table('landing_page_compares_section_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable();
            $table->string('brand')->nullable();
            $table->string('color')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('weight')->nullable();
            $table->string('attributes')->nullable();
            $table->string('cta_button')->nullable();
            $table->string('cta_button_text')->nullable();
            $table->string('cta_button_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_page_compares_section_items', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('brand');
            $table->dropColumn('color');
            $table->dropColumn('dimensions');
            $table->dropColumn('weight');
            $table->dropColumn('attributes');
            $table->dropColumn('cta_button');
            $table->dropColumn('cta_button_text');
            $table->dropColumn('cta_button_link');
        });
    }
};
