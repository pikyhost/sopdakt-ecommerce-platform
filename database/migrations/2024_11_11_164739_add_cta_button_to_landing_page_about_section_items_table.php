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
        Schema::table('landing_page_about_section_items', function (Blueprint $table) {
            $table->string('is_cta_button')->nullable()->default(0);
            $table->string('cta_button_text')->nullable();
            $table->string('cta_button_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_page_about_section_items', function (Blueprint $table) {
            $table->dropColumn('is_cta_button');
            $table->dropColumn('cta_button_text');
            $table->dropColumn('cta_button_link');
        });
    }
};
