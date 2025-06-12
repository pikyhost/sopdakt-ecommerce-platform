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
        Schema::table('landing_pages', function (Blueprint $table) {

            $table->string('home_title')->nullable()->change();
            $table->string('about_title')->nullable()->change();
            $table->string('feature_title')->nullable()->change();
            $table->string('product_title')->nullable()->change();
            $table->string('compare_title')->nullable()->change();
            $table->string('feedback_title')->nullable()->change();
            $table->string('faq_title')->nullable()->change();
            $table->string('footer_title')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->string('home_title')->nullable(false)->change();
            $table->string('about_title')->nullable(false)->change();
            $table->string('feature_title')->nullable(false)->change();
            $table->string('product_title')->nullable(false)->change();
            $table->string('compare_title')->nullable(false)->change();
            $table->string('feedback_title')->nullable(false)->change();
            $table->string('faq_title')->nullable(false)->change();
            $table->string('footer_title')->nullable(false)->change();
        });
    }
};
