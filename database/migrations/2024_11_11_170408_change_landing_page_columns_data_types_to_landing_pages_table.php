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

            $table->text('meta_title')->nullable()->change();
            $table->text('meta_description')->nullable()->change();
            $table->text('meta_keywords')->nullable()->change();
            $table->text('home_title')->nullable()->change();
            $table->text('home_subtitle')->nullable()->change();
            $table->text('home_cta_button_text')->nullable()->change();
            $table->text('home_cta_button_link')->nullable()->change();
            $table->text('about_title')->nullable()->change();
            $table->text('about_subtitle')->nullable()->change();
            $table->text('feature_title')->nullable()->change();
            $table->text('feature_subtitle')->nullable()->change();
            $table->text('feature_image')->nullable()->change();
            $table->text('product_title')->nullable()->change();
            $table->text('product_subtitle')->nullable()->change();
            $table->text('compare_title')->nullable()->change();
            $table->text('compare_subtitle')->nullable()->change();
            $table->text('feedback_title')->nullable()->change();
            $table->text('feedback_subtitle')->nullable()->change();
            $table->text('faq_title')->nullable()->change();
            $table->text('faq_subtitle')->nullable()->change();
            $table->text('faq_image')->nullable()->change();
            $table->text('footer_title')->nullable()->change();
            $table->text('footer_subtitle')->nullable()->change();
            $table->text('footer_image')->nullable()->change();
            $table->text('feature1_title')->nullable()->change();
            $table->text('feature1_subtitle')->nullable()->change();
            $table->text('feature1_image')->nullable()->change();
            $table->text('feature2_title')->nullable()->change();
            $table->text('feature2_subtitle')->nullable()->change();
            $table->text('feature2_image')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {

            $table->string('meta_title')->nullable()->change();
            $table->string('meta_description')->nullable()->change();
            $table->string('meta_keywords')->nullable()->change();
            $table->string('home_title')->nullable()->change();
            $table->string('home_subtitle')->nullable()->change();
            $table->string('home_cta_button_text')->nullable()->change();
            $table->string('home_cta_button_link')->nullable()->change();
            $table->string('about_title')->nullable()->change();
            $table->string('about_subtitle')->nullable()->change();
            $table->string('feature_title')->nullable()->change();
            $table->string('feature_subtitle')->nullable()->change();
            $table->string('feature_image')->nullable()->change();
            $table->string('product_title')->nullable()->change();
            $table->string('product_subtitle')->nullable()->change();
            $table->string('compare_title')->nullable()->change();
            $table->string('compare_subtitle')->nullable()->change();
            $table->string('feedback_title')->nullable()->change();
            $table->string('feedback_subtitle')->nullable()->change();
            $table->string('faq_title')->nullable()->change();
            $table->string('faq_subtitle')->nullable()->change();
            $table->string('faq_image')->nullable()->change();
            $table->string('footer_title')->nullable()->change();
            $table->string('footer_subtitle')->nullable()->change();
            $table->string('footer_image')->nullable()->change();
            $table->string('feature1_title')->nullable()->change();
            $table->string('feature1_subtitle')->nullable()->change();
            $table->string('feature1_image')->nullable()->change();
            $table->string('feature2_title')->nullable()->change();
            $table->string('feature2_subtitle')->nullable()->change();
            $table->string('feature2_image')->nullable()->change();
        });
    }
};
