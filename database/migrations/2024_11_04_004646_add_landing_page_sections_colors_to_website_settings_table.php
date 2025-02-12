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
            $table->dropColumn('landing_page_primary_text_color');
            $table->dropColumn('landing_page_primary_highlight_color');
            $table->dropColumn('landing_page_primary_accent_color');
            $table->dropColumn('landing_page_secondary_text_color');
            $table->dropColumn('landing_page_button_accent_background');
            $table->dropColumn('landing_page_button_accent_color');

            $table->string('sections_title_color')->default('#ffce56');
            $table->string('sections_subtitle_color')->default('#970404');
            $table->string('home_section_background_color')->default('#fbfbfb');
            $table->string('home_section_title_color')->default('#000000');
            $table->string('home_section_subtitle_color')->default('#780000');
            $table->string('about_section_background_color')->default('#021d89');
            $table->string('about_section_boxes_background_color')->default('#f7f8fc');
            $table->string('about_section_box_title_color_hover')->default('#ffce56');
            $table->string('about_section_box_subtitle_color_hover')->default('#ffce56');
            $table->string('features12_section_background_color')->default('#f7f8fc');
            $table->string('features12_section_box_title_color')->default('#be0101');
            $table->string('features12_section_box_subtitle_color')->default('#555555');
            $table->string('faq_background_color')->default('#f7f8fc');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->string('landing_page_primary_text_color')->default('#000000');
            $table->string('landing_page_primary_highlight_color')->default('#ffce56');
            $table->string('landing_page_primary_accent_color')->default('#ffce56');
            $table->string('landing_page_secondary_text_color')->default('#000000');
            $table->string('landing_page_button_accent_background')->default('#ffce56');
            $table->string('landing_page_button_accent_color')->default('#ffffff');

            $table->dropColumn('sections_title_color');
            $table->dropColumn('sections_subtitle_color');
            $table->dropColumn('home_section_background_color');
            $table->dropColumn('home_section_title_color');
            $table->dropColumn('home_section_subtitle_color');
            $table->dropColumn('about_section_background_color');
            $table->dropColumn('about_section_boxes_background_color');
            $table->dropColumn('about_section_box_title_color_hover');
            $table->dropColumn('about_section_box_subtitle_color_hover');
            $table->dropColumn('features12_section_background_color');
            $table->dropColumn('features12_section_box_title_color');
            $table->dropColumn('features12_section_box_subtitle_color');
            $table->dropColumn('faq_background_color');
        });
    }
};
