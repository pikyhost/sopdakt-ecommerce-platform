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
        Schema::create('landing_page_settings', function (Blueprint $table) {
            $table->id();
            $table->text('landing_page_primary_background_color')->nullable();
            $table->text('landing_page_primary_color')->nullable();
            $table->text('landing_page_accent_color')->nullable();
            $table->text('landing_page_primary_gradient_color')->nullable();
            $table->text('landing_page_primary_gradient_accent_color')->nullable();
            $table->text('sections_title_color')->nullable();
            $table->text('sections_subtitle_color')->nullable();
            $table->text('home_section_background_color')->nullable();
            $table->text('home_section_title_color')->nullable();
            $table->text('home_section_subtitle_color')->nullable();
            $table->text('about_section_background_color')->nullable();
            $table->text('about_section_boxes_background_color')->nullable();
            $table->text('about_section_box_title_color_hover')->nullable();
            $table->text('about_section_box_subtitle_color_hover')->nullable();
            $table->text('features12_section_background_color')->nullable();
            $table->text('features12_section_box_title_color')->nullable();
            $table->text('features12_section_box_subtitle_color')->nullable();
            $table->text('faq_background_color')->nullable();
            $table->text('feature3_section_background_color')->nullable();
            $table->text('feature3_section_box_background_color')->nullable();
            $table->text('feature3_section_box_title_color')->nullable();
            $table->text('feature3_section_box_subtitle_color')->nullable();
            $table->text('deal_of_the_week_section_background_color')->nullable();
            $table->text('deal_of_the_week_section_box_background_color')->nullable();
            $table->text('deal_of_the_week_section_box_title_color')->nullable();
            $table->text('deal_of_the_week_section_box_subtitle_color')->nullable();
            $table->text('deal_of_the_week_section_box_price_color')->nullable();
            $table->text('products_section_background_color')->nullable();
            $table->text('products_section_item_background_color')->nullable();
            $table->text('products_section_item_image_background_color')->nullable();
            $table->text('products_section_item_title_color')->nullable();
            $table->text('why_choose_us_section_background_color')->nullable();
            $table->text('compare_section_background_color')->nullable();
            $table->text('compare_section_product_title_color')->nullable();
            $table->text('compare_section_product_subtitle_color')->nullable();
            $table->text('compare_section_table_property_title_color')->nullable();
            $table->text('feedbacks_section_background_color')->nullable();
            $table->text('feedbacks_section_comment_color')->nullable();
            $table->text('feedbacks_section_client_name_color')->nullable();
            $table->text('feedbacks_section_client_position_color')->nullable();
            $table->text('feedbacks_section_box_background_color')->nullable();
            $table->text('faq_section_question_background_color')->nullable();
            $table->text('faq_section_question_color')->nullable();
            $table->text('faq_section_answer_background_color')->nullable();
            $table->text('faq_section_answer_color')->nullable();
            $table->text('contact_section_background_color')->nullable();
            $table->text('contact_section_boxes_text_color')->nullable();
            $table->text('contact_section_box_background_color')->nullable();
            $table->text('contact_section_form_input_field_background_color')->nullable();
            $table->text('contact_section_form_input_field_text_color')->nullable();
            $table->text('footer_section_background_color')->nullable();
            $table->text('footer_section_subtitle_color')->nullable();
            $table->text('about_section_title_color')->nullable();
            $table->text('about_section_subtitle_color')->nullable();

            $table->text('features1_section_title_color')->nullable();
            $table->text('features1_section_subtitle_color')->nullable();
            $table->text('features2_section_title_color')->nullable();
            $table->text('features2_section_subtitle_color')->nullable();
            $table->text('feature3_section_title_color')->nullable();
            $table->text('feature3_section_subtitle_color')->nullable();
            $table->text('deel_of_the_week_section_title_color')->nullable();
            $table->text('deel_of_the_week_section_subtitle_color')->nullable();
            $table->text('products_section_title_color')->nullable();
            $table->text('products_section_subtitle_color')->nullable();
            $table->text('why_choose_us_section_title_color')->nullable();
            $table->text('why_choose_us_section_subtitle_color')->nullable();
            $table->text('compare_section_title_color')->nullable();
            $table->text('compare_section_subtitle_color')->nullable();
            $table->text('feedbacks_section_title_color')->nullable();
            $table->text('feedbacks_section_subtitle_color')->nullable();
            $table->text('faq_section_title_color')->nullable();
            $table->text('faq_section_subtitle_color')->nullable();
            $table->text('contact_section_title_color')->nullable();
            $table->text('contact_section_subtitle_color')->nullable();
            $table->text('deal_of_the_week_counter_color')->nullable();

            // Add text columns for navbar and other sections
            $table->text('nav_bar_items_not_active_text_color')->nullable();
            $table->text('nav_bar_items_text_color')->nullable();
            $table->text('nav_bar_background_color')->nullable();
            $table->text('home_section_img_circles_color')->nullable();

            // Header image
            $table->text('landing_page_header_image')->nullable();

            // CTA button colors for various sections
            $table->text('deal_of_the_week_section_cta_button_background_color')->nullable();
            $table->text('deal_of_the_week_section_cta_button_hover_background_color')->nullable();
            $table->text('deal_of_the_week_section_cta_button_border_color')->nullable();
            $table->text('deal_of_the_week_section_cta_button_text_hover_color')->nullable();
            $table->text('deal_of_the_week_section_cta_button_text_color')->nullable();

            $table->text('products_section_cta_button_background_color')->nullable();
            $table->text('products_section_cta_button_hover_background_color')->nullable();
            $table->text('products_section_cta_button_border_color')->nullable();
            $table->text('products_section_cta_button_text_hover_color')->nullable();
            $table->text('products_section_cta_button_text_color')->nullable();

            $table->text('compare_section_cta_button_background_color')->nullable();
            $table->text('compare_section_cta_button_hover_background_color')->nullable();
            $table->text('compare_section_cta_button_border_color')->nullable();
            $table->text('compare_section_cta_button_text_hover_color')->nullable();
            $table->text('compare_section_cta_button_text_color')->nullable();

            $table->text('home_section_cta_button_background_color')->nullable();
            $table->text('home_section_cta_button_hover_background_color')->nullable();
            $table->text('home_section_cta_button_border_color')->nullable();
            $table->text('home_section_cta_button_text_hover_color')->nullable();
            $table->text('home_section_cta_button_text_color')->nullable();

            $table->text('about_section_cta_button_background_color')->nullable();
            $table->text('about_section_cta_button_hover_background_color')->nullable();
            $table->text('about_section_cta_button_border_color')->nullable();
            $table->text('about_section_cta_button_text_hover_color')->nullable();
            $table->text('about_section_cta_button_text_color')->nullable();

            // Features section CTA button colors
            $table->text('features1_section_cta_button_background_color')->nullable();
            $table->text('features1_section_cta_button_hover_background_color')->nullable();
            $table->text('features1_section_cta_button_border_color')->nullable();
            $table->text('features1_section_cta_button_text_hover_color')->nullable();
            $table->text('features1_section_cta_button_text_color')->nullable();

            $table->text('features2_section_cta_button_background_color')->nullable();
            $table->text('features2_section_cta_button_hover_background_color')->nullable();
            $table->text('features2_section_cta_button_border_color')->nullable();
            $table->text('features2_section_cta_button_text_hover_color')->nullable();
            $table->text('features2_section_cta_button_text_color')->nullable();

            // Top bar colors
            $table->text('top_bar_background_color')->nullable();
            $table->text('top_bar_text_color')->nullable();
            $table->text('top_bar_dark_background_color')->nullable();
            $table->text('top_bar_dark_text_color')->nullable();

            // Counter section colors
            $table->text('counter_section_background_color')->nullable();
            $table->text('counter_section_counter_color')->nullable();
            $table->text('counter_section_cta_button_color')->nullable();
            $table->text('counter_section_cta_button_text_color')->nullable();

            // Product details colors
            $table->text('product_details_background_color')->nullable();
            $table->text('product_details_title_color')->nullable();
            $table->text('product_details_description_color')->nullable();
            $table->text('product_details_price_color')->nullable();
            $table->text('product_details_old_price_color')->nullable();
            $table->text('product_details_feature_background_color')->nullable();
            $table->text('product_details_feature_text_color')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_settings');
    }
};
