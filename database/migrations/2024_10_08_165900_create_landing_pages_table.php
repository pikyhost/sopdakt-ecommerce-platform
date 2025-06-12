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
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();

            $table->tinyInteger('is_home')->default(0);
            $table->tinyInteger('is_about')->default(0);
            $table->tinyInteger('is_features')->default(0);
            $table->tinyInteger('is_products')->default(0);
            $table->tinyInteger('is_compare')->default(0);
            $table->tinyInteger('is_feedbacks')->default(0);
            $table->tinyInteger('is_faq')->default(0);
            $table->tinyInteger('is_footer')->default(0);

            $table->string('slug')->unique();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->tinyInteger('status')->default(1);

            $table->string('home_image')->nullable();
            $table->string('home_title');
            $table->string('home_subtitle')->nullable();
            $table->tinyInteger('home_cta_button')->default(0);
            $table->string('home_cta_button_text')->nullable();
            $table->string('home_cta_button_link')->nullable();

            $table->string('about_title');
            $table->string('about_subtitle')->nullable();

            $table->string('feature_title');
            $table->string('feature_subtitle')->nullable();
            $table->string('feature_image')->nullable();

            $table->string('product_title');
            $table->string('product_subtitle')->nullable();

            $table->string('compare_title');
            $table->string('compare_subtitle')->nullable();


            $table->string('feedback_title');
            $table->string('feedback_subtitle')->nullable();

            $table->string('faq_title');
            $table->string('faq_subtitle')->nullable();
            $table->string('faq_image')->nullable();

            $table->string('footer_title');
            $table->string('footer_subtitle')->nullable();
            $table->string('footer_image')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
