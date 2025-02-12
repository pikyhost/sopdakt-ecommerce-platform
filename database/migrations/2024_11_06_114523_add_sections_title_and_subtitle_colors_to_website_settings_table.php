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
        Schema::table('website_settings', function (Blueprint $table) {
//            --about-section-title-color: #ff0000;
//    --about-section-subtitle-color: #ff0000;
//    --features1-section-title-color: #ff0000;
//    --features1-section-subtitle-color: #ff0000;
//    --features2-section-title-color: #ff0000;
//    --features2-section-subtitle-color: #ff0000;
//    --feature3-section-title-color: #ff0000;
//    --feature3-section-subtitle-color: #ff0000;
//    --deel-of-the-week-section-title-color: #ff0000;
//    --deel-of-the-week-section-subtitle-color: #ff0000;
//    --products-section-title-color: #ff0000;
//    --products-section-subtitle-color: #ff0000;
//    --why-choose-us-section-title-color: #ff0000;
//    --why-choose-us-section-subtitle-color: #ff0000;
//    --compare-section-title-color: #ff0000;
//    --compare-section-subtitle-color: #ff0000;
//    --feedbacks-section-title-color: #ff0000;
//    --feedbacks-section-subtitle-color: #ff0000;
//    --faq-section-title-color: #ff0000;
//    --faq-section-subtitle-color: #ff0000;
//    --contact-section-title-color: #ff0000;
//    --contact-section-subtitle-color: #ff0000;

            $table->string('about_section_title_color')->nullable();
            $table->string('about_section_subtitle_color')->nullable();
            $table->string('features1_section_title_color')->nullable();
            $table->string('features1_section_subtitle_color')->nullable();
            $table->string('features2_section_title_color')->nullable();
            $table->string('features2_section_subtitle_color')->nullable();
            $table->string('feature3_section_title_color')->nullable();
            $table->string('feature3_section_subtitle_color')->nullable();
            $table->string('deel_of_the_week_section_title_color')->nullable();
            $table->string('deel_of_the_week_section_subtitle_color')->nullable();
            $table->string('products_section_title_color')->nullable();
            $table->string('products_section_subtitle_color')->nullable();
            $table->string('why_choose_us_section_title_color')->nullable();
            $table->string('why_choose_us_section_subtitle_color')->nullable();
            $table->string('compare_section_title_color')->nullable();
            $table->string('compare_section_subtitle_color')->nullable();
            $table->string('feedbacks_section_title_color')->nullable();
            $table->string('feedbacks_section_subtitle_color')->nullable();
            $table->string('faq_section_title_color')->nullable();
            $table->string('faq_section_subtitle_color')->nullable();
            $table->string('contact_section_title_color')->nullable();
            $table->string('contact_section_subtitle_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn([
                'about_section_title_color',
                'about_section_subtitle_color',
                'features1_section_title_color',
                'features1_section_subtitle_color',
                'features2_section_title_color',
                'features2_section_subtitle_color',
                'feature3_section_title_color',
                'feature3_section_subtitle_color',
                'deel_of_the_week_section_title_color',
                'deel_of_the_week_section_subtitle_color',
                'products_section_title_color',
                'products_section_subtitle_color',
                'why_choose_us_section_title_color',
                'why_choose_us_section_subtitle_color',
                'compare_section_title_color',
                'compare_section_subtitle_color',
                'feedbacks_section_title_color',
                'feedbacks_section_subtitle_color',
                'faq_section_title_color',
                'faq_section_subtitle_color',
                'contact_section_title_color',
                'contact_section_subtitle_color',
            ]);
        });
    }
};
