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
        Schema::table('website_settings', function (Blueprint $table) {
            if (Schema::hasColumn('website_settings', 'landing_page_primary_background_color')) {
                $table->dropColumn('landing_page_primary_background_color');
            }

            if (Schema::hasColumn('website_settings', 'landing_page_primary_color')) {
                $table->dropColumn('landing_page_primary_color');
            }
            if (Schema::hasColumn('website_settings', 'landing_page_accent_color')) {
                $table->dropColumn('landing_page_accent_color');
            }
            if (Schema::hasColumn('website_settings', 'landing_page_primary_gradient_color')) {
                $table->dropColumn('landing_page_primary_gradient_color');
            }
            if (Schema::hasColumn('website_settings', 'landing_page_primary_gradient_accent_color')) {
                $table->dropColumn('landing_page_primary_gradient_accent_color');
            }
            if (Schema::hasColumn('website_settings', 'sections_title_color')) {
                $table->dropColumn('sections_title_color');
            }
            if (Schema::hasColumn('website_settings', 'sections_subtitle_color')) {
                $table->dropColumn('sections_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'home_section_background_color')) {
                $table->dropColumn('home_section_background_color');
            }
            if (Schema::hasColumn('website_settings', 'home_section_title_color')) {
                $table->dropColumn('home_section_title_color');
            }
            if (Schema::hasColumn('website_settings', 'home_section_subtitle_color')) {
                $table->dropColumn('home_section_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'about_section_background_color')) {
                $table->dropColumn('about_section_background_color');
            }
            if (Schema::hasColumn('website_settings', 'about_section_boxes_background_color')) {
                $table->dropColumn('about_section_boxes_background_color');
            }
            if (Schema::hasColumn('website_settings', 'about_section_box_title_color_hover')) {
                $table->dropColumn('about_section_box_title_color_hover');
            }
            if (Schema::hasColumn('website_settings', 'about_section_box_subtitle_color_hover')) {
                $table->dropColumn('about_section_box_subtitle_color_hover');
            }
            if (Schema::hasColumn('website_settings', 'features12_section_background_color')) {
                $table->dropColumn('features12_section_background_color');
            }
            if (Schema::hasColumn('website_settings', 'features12_section_box_title_color')) {
                $table->dropColumn('features12_section_box_title_color');
            }
            if (Schema::hasColumn('website_settings', 'features12_section_box_subtitle_color')) {
                $table->dropColumn('features12_section_box_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'faq_background_color')) {
                $table->dropColumn('faq_background_color');
            }
            if (Schema::hasColumn('website_settings', 'feature3_section_background_color')) {
                $table->dropColumn('feature3_section_background_color');
            }
            if (Schema::hasColumn('website_settings', 'feature3_section_box_background_color')) {
                $table->dropColumn('feature3_section_box_background_color');
            }
            if (Schema::hasColumn('website_settings', 'feature3_section_box_title_color')) {
                $table->dropColumn('feature3_section_box_title_color');
            }
            if (Schema::hasColumn('website_settings', 'feature3_section_box_subtitle_color')) {
                $table->dropColumn('feature3_section_box_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'deal_of_the_week_section_background_color')) {
                $table->dropColumn('deal_of_the_week_section_background_color');
            }
            if (Schema::hasColumn('website_settings', 'deal_of_the_week_section_box_background_color')) {
                $table->dropColumn('deal_of_the_week_section_box_background_color');
            }
            if (Schema::hasColumn('website_settings', 'deal_of_the_week_section_box_title_color')) {
                $table->dropColumn('deal_of_the_week_section_box_title_color');
            }
            if (Schema::hasColumn('website_settings', 'deal_of_the_week_section_box_subtitle_color')) {
                $table->dropColumn('deal_of_the_week_section_box_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'deal_of_the_week_section_box_price_color')) {
                $table->dropColumn('deal_of_the_week_section_box_price_color');
            }
            if (Schema::hasColumn('website_settings', 'products_section_background_color')) {
                $table->dropColumn('products_section_background_color');
            }
            if (Schema::hasColumn('website_settings', 'products_section_item_background_color')) {
                $table->dropColumn('products_section_item_background_color');
            }
            if (Schema::hasColumn('website_settings', 'products_section_item_image_background_color')) {
                $table->dropColumn('products_section_item_image_background_color');
            }
            if (Schema::hasColumn('website_settings', 'products_section_item_title_color')) {
                $table->dropColumn('products_section_item_title_color');
            }
            if (Schema::hasColumn('website_settings', 'why_choose_us_section_background_color')) {
                $table->dropColumn('why_choose_us_section_background_color');
            }
            if (Schema::hasColumn('website_settings', 'compare_section_background_color')) {
                $table->dropColumn('compare_section_background_color');
            }
            if (Schema::hasColumn('website_settings', 'compare_section_product_title_color')) {
                $table->dropColumn('compare_section_product_title_color');
            }
            if (Schema::hasColumn('website_settings', 'compare_section_product_subtitle_color')) {
                $table->dropColumn('compare_section_product_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'compare_section_table_property_title_color')) {
                $table->dropColumn('compare_section_table_property_title_color');
            }
            if (Schema::hasColumn('website_settings', 'feedbacks_section_background_color')) {
                $table->dropColumn('feedbacks_section_background_color');
            }
            if (Schema::hasColumn('website_settings', 'feedbacks_section_comment_color')) {
                $table->dropColumn('feedbacks_section_comment_color');
            }
            if (Schema::hasColumn('website_settings', 'feedbacks_section_client_name_color')) {
                $table->dropColumn('feedbacks_section_client_name_color');
            }
            if (Schema::hasColumn('website_settings', 'feedbacks_section_client_position_color')) {
                $table->dropColumn('feedbacks_section_client_position_color');
            }
            if (Schema::hasColumn('website_settings', 'feedbacks_section_box_background_color')) {
                $table->dropColumn('feedbacks_section_box_background_color');
            }
            if (Schema::hasColumn('website_settings', 'faq_section_question_background_color')) {
                $table->dropColumn('faq_section_question_background_color');
            }
            if (Schema::hasColumn('website_settings', 'faq_section_question_color')) {
                $table->dropColumn('faq_section_question_color');
            }
            if (Schema::hasColumn('website_settings', 'faq_section_answer_background_color')) {
                $table->dropColumn('faq_section_answer_background_color');
            }
            if (Schema::hasColumn('website_settings', 'faq_section_answer_color')) {
                $table->dropColumn('faq_section_answer_color');
            }
            if (Schema::hasColumn('website_settings', 'contact_section_background_color')) {
                $table->dropColumn('contact_section_background_color');
            }
            if (Schema::hasColumn('website_settings', 'contact_section_boxes_dropColumn_color')) {
                $table->dropColumn('contact_section_boxes_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'contact_section_box_background_color')) {
                $table->dropColumn('contact_section_box_background_color');
            }
            if (Schema::hasColumn('website_settings', 'contact_section_form_input_field_background_color')) {
                $table->dropColumn('contact_section_form_input_field_background_color');
            }
            if (Schema::hasColumn('website_settings', 'contact_section_form_input_field_dropColumn_color')) {
                $table->dropColumn('contact_section_form_input_field_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'footer_section_background_color')) {
                $table->dropColumn('footer_section_background_color');
            }
            if (Schema::hasColumn('website_settings', 'footer_section_subtitle_color')) {
                $table->dropColumn('footer_section_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'about_section_title_color')) {
                $table->dropColumn('about_section_title_color');
            }
            if (Schema::hasColumn('website_settings', 'about_section_subtitle_color')) {
                $table->dropColumn('about_section_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'features1_section_title_color')) {
                $table->dropColumn('features1_section_title_color');
            }
            if (Schema::hasColumn('website_settings', 'features1_section_subtitle_color')) {
                $table->dropColumn('features1_section_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'features2_section_title_color')) {
                $table->dropColumn('features2_section_title_color');
            }
            if (Schema::hasColumn('website_settings', 'features2_section_subtitle_color')) {
                $table->dropColumn('features2_section_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'feature3_section_title_color')) {
                $table->dropColumn('feature3_section_title_color');
            }
            if (Schema::hasColumn('website_settings', 'feature3_section_subtitle_color')) {
                $table->dropColumn('feature3_section_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'deel_of_the_week_section_title_color')) {
                $table->dropColumn('deel_of_the_week_section_title_color');
            }
            if (Schema::hasColumn('website_settings', 'deel_of_the_week_section_subtitle_color')) {
                $table->dropColumn('deel_of_the_week_section_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'products_section_title_color')) {
                $table->dropColumn('products_section_title_color');
            }
            if (Schema::hasColumn('website_settings', 'products_section_subtitle_color')) {
                $table->dropColumn('products_section_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'why_choose_us_section_title_color')) {
                $table->dropColumn('why_choose_us_section_title_color');
            }
            if (Schema::hasColumn('website_settings', 'why_choose_us_section_subtitle_color')) {
                $table->dropColumn('why_choose_us_section_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'compare_section_title_color')) {
                $table->dropColumn('compare_section_title_color');
            }
            if (Schema::hasColumn('website_settings', 'compare_section_subtitle_color')) {
                $table->dropColumn('compare_section_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'feedbacks_section_title_color')) {
                $table->dropColumn('feedbacks_section_title_color');
            }
            if (Schema::hasColumn('website_settings', 'feedbacks_section_subtitle_color')) {
                $table->dropColumn('feedbacks_section_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'faq_section_title_color')) {
                $table->dropColumn('faq_section_title_color');
            }
            if (Schema::hasColumn('website_settings', 'faq_section_subtitle_color')) {
                $table->dropColumn('faq_section_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'contact_section_title_color')) {
                $table->dropColumn('contact_section_title_color');
            }
            if (Schema::hasColumn('website_settings', 'contact_section_subtitle_color')) {
                $table->dropColumn('contact_section_subtitle_color');
            }
            if (Schema::hasColumn('website_settings', 'deal_of_the_week_counter_color')) {
                $table->dropColumn('deal_of_the_week_counter_color');
            }
            if (Schema::hasColumn('website_settings', 'nav_bar_items_not_active_dropColumn_color')) {
                $table->dropColumn('nav_bar_items_not_active_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'nav_bar_items_dropColumn_color')) {
                $table->dropColumn('nav_bar_items_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'nav_bar_background_color')) {
                $table->dropColumn('nav_bar_background_color');
            }
            if (Schema::hasColumn('website_settings', 'home_section_img_circles_color')) {
                $table->dropColumn('home_section_img_circles_color');
            }
            if (Schema::hasColumn('website_settings', 'landing_page_header_image')) {
                $table->dropColumn('landing_page_header_image');
            }
            if (Schema::hasColumn('website_settings', 'deal_of_the_week_section_cta_button_background_color')) {
                $table->dropColumn('deal_of_the_week_section_cta_button_background_color');
            }
//            if (Schema::hasColumn('website_settings', 'deal_of_the_week_section_cta_button_hover_background_color')) {
//                $table->dropColumn('deal_of_the_week_section_cta_button_hover_background_color');
//            }
            if (Schema::hasColumn('website_settings', 'deal_of_the_week_section_cta_button_border_color')) {
                $table->dropColumn('deal_of_the_week_section_cta_button_border_color');
            }
            if (Schema::hasColumn('website_settings', 'deal_of_the_week_section_cta_button_dropColumn_hover_color')) {
                $table->dropColumn('deal_of_the_week_section_cta_button_dropColumn_hover_color');
            }
            if (Schema::hasColumn('website_settings', 'deal_of_the_week_section_cta_button_dropColumn_color')) {
                $table->dropColumn('deal_of_the_week_section_cta_button_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'products_section_cta_button_background_color')) {
                $table->dropColumn('products_section_cta_button_background_color');
            }
            if (Schema::hasColumn('website_settings', 'products_section_cta_button_hover_background_color')) {
                $table->dropColumn('products_section_cta_button_hover_background_color');
            }
            if (Schema::hasColumn('website_settings', 'products_section_cta_button_border_color')) {
                $table->dropColumn('products_section_cta_button_border_color');
            }
            if (Schema::hasColumn('website_settings', 'products_section_cta_button_dropColumn_hover_color')) {
                $table->dropColumn('products_section_cta_button_dropColumn_hover_color');
            }
            if (Schema::hasColumn('website_settings', 'products_section_cta_button_dropColumn_color')) {
                $table->dropColumn('products_section_cta_button_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'compare_section_cta_button_background_color')) {
                $table->dropColumn('compare_section_cta_button_background_color');
            }
            if (Schema::hasColumn('website_settings', 'compare_section_cta_button_hover_background_color')) {
                $table->dropColumn('compare_section_cta_button_hover_background_color');
            }
            if (Schema::hasColumn('website_settings', 'compare_section_cta_button_border_color')) {
                $table->dropColumn('compare_section_cta_button_border_color');
            }
            if (Schema::hasColumn('website_settings', 'compare_section_cta_button_dropColumn_hover_color')) {
                $table->dropColumn('compare_section_cta_button_dropColumn_hover_color');
            }
            if (Schema::hasColumn('website_settings', 'compare_section_cta_button_dropColumn_color')) {
                $table->dropColumn('compare_section_cta_button_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'home_section_cta_button_background_color')) {
                $table->dropColumn('home_section_cta_button_background_color');
            }
            if (Schema::hasColumn('website_settings', 'home_section_cta_button_hover_background_color')) {
                $table->dropColumn('home_section_cta_button_hover_background_color');
            }
            if (Schema::hasColumn('website_settings', 'home_section_cta_button_border_color')) {
                $table->dropColumn('home_section_cta_button_border_color');
            }
            if (Schema::hasColumn('website_settings', 'home_section_cta_button_dropColumn_hover_color')) {
                $table->dropColumn('home_section_cta_button_dropColumn_hover_color');
            }
            if (Schema::hasColumn('website_settings', 'home_section_cta_button_dropColumn_color')) {
                $table->dropColumn('home_section_cta_button_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'about_section_cta_button_background_color')) {
                $table->dropColumn('about_section_cta_button_background_color');
            }
            if (Schema::hasColumn('website_settings', 'about_section_cta_button_hover_background_color')) {
                $table->dropColumn('about_section_cta_button_hover_background_color');
            }
            if (Schema::hasColumn('website_settings', 'about_section_cta_button_border_color')) {
                $table->dropColumn('about_section_cta_button_border_color');
            }
            if (Schema::hasColumn('website_settings', 'about_section_cta_button_dropColumn_hover_color')) {
                $table->dropColumn('about_section_cta_button_dropColumn_hover_color');
            }
            if (Schema::hasColumn('website_settings', 'about_section_cta_button_dropColumn_color')) {
                $table->dropColumn('about_section_cta_button_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'features1_section_cta_button_background_color')) {
                $table->dropColumn('features1_section_cta_button_background_color');
            }
            if (Schema::hasColumn('website_settings', 'features1_section_cta_button_hover_background_color')) {
                $table->dropColumn('features1_section_cta_button_hover_background_color');
            }
            if (Schema::hasColumn('website_settings', 'features1_section_cta_button_border_color')) {
                $table->dropColumn('features1_section_cta_button_border_color');
            }
            if (Schema::hasColumn('website_settings', 'features1_section_cta_button_dropColumn_hover_color')) {
                $table->dropColumn('features1_section_cta_button_dropColumn_hover_color');
            }
            if (Schema::hasColumn('website_settings', 'features1_section_cta_button_dropColumn_color')) {
                $table->dropColumn('features1_section_cta_button_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'features2_section_cta_button_background_color')) {
                $table->dropColumn('features2_section_cta_button_background_color');
            }
            if (Schema::hasColumn('website_settings', 'features2_section_cta_button_hover_background_color')) {
                $table->dropColumn('features2_section_cta_button_hover_background_color');
            }
            if (Schema::hasColumn('website_settings', 'features2_section_cta_button_border_color')) {
                $table->dropColumn('features2_section_cta_button_border_color');
            }
            if (Schema::hasColumn('website_settings', 'features2_section_cta_button_dropColumn_hover_color')) {
                $table->dropColumn('features2_section_cta_button_dropColumn_hover_color');
            }
            if (Schema::hasColumn('website_settings', 'features2_section_cta_button_dropColumn_color')) {
                $table->dropColumn('features2_section_cta_button_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'top_bar_background_color')) {
                $table->dropColumn('top_bar_background_color');
            }
            if (Schema::hasColumn('website_settings', 'top_bar_dropColumn_color')) {
                $table->dropColumn('top_bar_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'top_bar_dark_background_color')) {
                $table->dropColumn('top_bar_dark_background_color');
            }
            if (Schema::hasColumn('website_settings', 'top_bar_dark_dropColumn_color')) {
                $table->dropColumn('top_bar_dark_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'counter_section_background_color')) {
                $table->dropColumn('counter_section_background_color');
            }
            if (Schema::hasColumn('website_settings', 'counter_section_counter_color')) {
                $table->dropColumn('counter_section_counter_color');
            }
            if (Schema::hasColumn('website_settings', 'counter_section_cta_button_color')) {
                $table->dropColumn('counter_section_cta_button_color');
            }
            if (Schema::hasColumn('website_settings', 'counter_section_cta_button_dropColumn_color')) {
                $table->dropColumn('counter_section_cta_button_dropColumn_color');
            }
            if (Schema::hasColumn('website_settings', 'product_details_background_color')) {
                $table->dropColumn('product_details_background_color');
            }
            if (Schema::hasColumn('website_settings', 'product_details_title_color')) {
                $table->dropColumn('product_details_title_color');
            }
            if (Schema::hasColumn('website_settings', 'product_details_description_color')) {
                $table->dropColumn('product_details_description_color');
            }
            if (Schema::hasColumn('website_settings', 'product_details_price_color')) {
                $table->dropColumn('product_details_price_color');
            }
            if (Schema::hasColumn('website_settings', 'product_details_old_price_color')) {
                $table->dropColumn('product_details_old_price_color');
            }
            if (Schema::hasColumn('website_settings', 'product_details_feature_background_color')) {
                $table->dropColumn('product_details_feature_background_color');
            }
            if (Schema::hasColumn('website_settings', 'product_details_feature_dropColumn_color')) {
                $table->dropColumn('product_details_feature_dropColumn_color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
//        Schema::table('website_settings', function (Blueprint $table) {
//            if (!Schema::hasColumn('website_settings', 'landing_page_primary_background_color')) {
//                $table->text('landing_page_primary_background_color');
//            }
//
//            if (!Schema::hasColumn('website_settings', 'landing_page_primary_color')) {
//                $table->text('landing_page_primary_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'landing_page_accent_color')) {
//                $table->text('landing_page_accent_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'landing_page_primary_gradient_color')) {
//                $table->text('landing_page_primary_gradient_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'landing_page_primary_gradient_accent_color')) {
//                $table->text('landing_page_primary_gradient_accent_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'sections_title_color')) {
//                $table->text('sections_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'sections_subtitle_color')) {
//                $table->text('sections_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'home_section_background_color')) {
//                $table->text('home_section_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'home_section_title_color')) {
//                $table->text('home_section_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'home_section_subtitle_color')) {
//                $table->text('home_section_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'about_section_background_color')) {
//                $table->text('about_section_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'about_section_boxes_background_color')) {
//                $table->text('about_section_boxes_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'about_section_box_title_color_hover')) {
//                $table->text('about_section_box_title_color_hover');
//            }
//            if (!Schema::hasColumn('website_settings', 'about_section_box_subtitle_color_hover')) {
//                $table->text('about_section_box_subtitle_color_hover');
//            }
//            if (!Schema::hasColumn('website_settings', 'features12_section_background_color')) {
//                $table->text('features12_section_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features12_section_box_title_color')) {
//                $table->text('features12_section_box_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features12_section_box_subtitle_color')) {
//                $table->text('features12_section_box_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'faq_background_color')) {
//                $table->text('faq_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'feature3_section_background_color')) {
//                $table->text('feature3_section_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'feature3_section_box_background_color')) {
//                $table->text('feature3_section_box_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'feature3_section_box_title_color')) {
//                $table->text('feature3_section_box_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'feature3_section_box_subtitle_color')) {
//                $table->text('feature3_section_box_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'deal_of_the_week_section_background_color')) {
//                $table->text('deal_of_the_week_section_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'deal_of_the_week_section_box_background_color')) {
//                $table->text('deal_of_the_week_section_box_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'deal_of_the_week_section_box_title_color')) {
//                $table->text('deal_of_the_week_section_box_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'deal_of_the_week_section_box_subtitle_color')) {
//                $table->text('deal_of_the_week_section_box_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'deal_of_the_week_section_box_price_color')) {
//                $table->text('deal_of_the_week_section_box_price_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'products_section_background_color')) {
//                $table->text('products_section_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'products_section_item_background_color')) {
//                $table->text('products_section_item_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'products_section_item_image_background_color')) {
//                $table->text('products_section_item_image_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'products_section_item_title_color')) {
//                $table->text('products_section_item_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'why_choose_us_section_background_color')) {
//                $table->text('why_choose_us_section_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'compare_section_background_color')) {
//                $table->text('compare_section_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'compare_section_product_title_color')) {
//                $table->text('compare_section_product_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'compare_section_product_subtitle_color')) {
//                $table->text('compare_section_product_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'compare_section_table_property_title_color')) {
//                $table->text('compare_section_table_property_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'feedbacks_section_background_color')) {
//                $table->text('feedbacks_section_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'feedbacks_section_comment_color')) {
//                $table->text('feedbacks_section_comment_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'feedbacks_section_client_name_color')) {
//                $table->text('feedbacks_section_client_name_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'feedbacks_section_client_position_color')) {
//                $table->text('feedbacks_section_client_position_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'feedbacks_section_box_background_color')) {
//                $table->text('feedbacks_section_box_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'faq_section_question_background_color')) {
//                $table->text('faq_section_question_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'faq_section_question_color')) {
//                $table->text('faq_section_question_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'faq_section_answer_background_color')) {
//                $table->text('faq_section_answer_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'faq_section_answer_color')) {
//                $table->text('faq_section_answer_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'contact_section_background_color')) {
//                $table->text('contact_section_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'contact_section_boxes_text_color')) {
//                $table->text('contact_section_boxes_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'contact_section_box_background_color')) {
//                $table->text('contact_section_box_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'contact_section_form_input_field_background_color')) {
//                $table->text('contact_section_form_input_field_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'contact_section_form_input_field_text_color')) {
//                $table->text('contact_section_form_input_field_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'footer_section_background_color')) {
//                $table->text('footer_section_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'footer_section_subtitle_color')) {
//                $table->text('footer_section_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'about_section_title_color')) {
//                $table->text('about_section_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'about_section_subtitle_color')) {
//                $table->text('about_section_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features1_section_title_color')) {
//                $table->text('features1_section_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features1_section_subtitle_color')) {
//                $table->text('features1_section_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features2_section_title_color')) {
//                $table->text('features2_section_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features2_section_subtitle_color')) {
//                $table->text('features2_section_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'feature3_section_title_color')) {
//                $table->text('feature3_section_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'feature3_section_subtitle_color')) {
//                $table->text('feature3_section_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'deel_of_the_week_section_title_color')) {
//                $table->text('deel_of_the_week_section_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'deel_of_the_week_section_subtitle_color')) {
//                $table->text('deel_of_the_week_section_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'products_section_title_color')) {
//                $table->text('products_section_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'products_section_subtitle_color')) {
//                $table->text('products_section_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'why_choose_us_section_title_color')) {
//                $table->text('why_choose_us_section_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'why_choose_us_section_subtitle_color')) {
//                $table->text('why_choose_us_section_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'compare_section_title_color')) {
//                $table->text('compare_section_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'compare_section_subtitle_color')) {
//                $table->text('compare_section_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'feedbacks_section_title_color')) {
//                $table->text('feedbacks_section_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'feedbacks_section_subtitle_color')) {
//                $table->text('feedbacks_section_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'faq_section_title_color')) {
//                $table->text('faq_section_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'faq_section_subtitle_color')) {
//                $table->text('faq_section_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'contact_section_title_color')) {
//                $table->text('contact_section_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'contact_section_subtitle_color')) {
//                $table->text('contact_section_subtitle_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'deal_of_the_week_counter_color')) {
//                $table->text('deal_of_the_week_counter_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'nav_bar_items_not_active_text_color')) {
//                $table->text('nav_bar_items_not_active_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'nav_bar_items_text_color')) {
//                $table->text('nav_bar_items_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'nav_bar_background_color')) {
//                $table->text('nav_bar_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'home_section_img_circles_color')) {
//                $table->text('home_section_img_circles_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'landing_page_header_image')) {
//                $table->text('landing_page_header_image');
//            }
//            if (!Schema::hasColumn('website_settings', 'deal_of_the_week_section_cta_button_background_color')) {
//                $table->text('deal_of_the_week_section_cta_button_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'deal_of_the_week_section_cta_button_hover_background_color')) {
//                $table->text('deal_of_the_week_section_cta_button_hover_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'deal_of_the_week_section_cta_button_border_color')) {
//                $table->text('deal_of_the_week_section_cta_button_border_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'deal_of_the_week_section_cta_button_text_hover_color')) {
//                $table->text('deal_of_the_week_section_cta_button_text_hover_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'deal_of_the_week_section_cta_button_text_color')) {
//                $table->text('deal_of_the_week_section_cta_button_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'products_section_cta_button_background_color')) {
//                $table->text('products_section_cta_button_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'products_section_cta_button_hover_background_color')) {
//                $table->text('products_section_cta_button_hover_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'products_section_cta_button_border_color')) {
//                $table->text('products_section_cta_button_border_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'products_section_cta_button_text_hover_color')) {
//                $table->text('products_section_cta_button_text_hover_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'products_section_cta_button_text_color')) {
//                $table->text('products_section_cta_button_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'compare_section_cta_button_background_color')) {
//                $table->text('compare_section_cta_button_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'compare_section_cta_button_hover_background_color')) {
//                $table->text('compare_section_cta_button_hover_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'compare_section_cta_button_border_color')) {
//                $table->text('compare_section_cta_button_border_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'compare_section_cta_button_text_hover_color')) {
//                $table->text('compare_section_cta_button_text_hover_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'compare_section_cta_button_text_color')) {
//                $table->text('compare_section_cta_button_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'home_section_cta_button_background_color')) {
//                $table->text('home_section_cta_button_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'home_section_cta_button_hover_background_color')) {
//                $table->text('home_section_cta_button_hover_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'home_section_cta_button_border_color')) {
//                $table->text('home_section_cta_button_border_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'home_section_cta_button_text_hover_color')) {
//                $table->text('home_section_cta_button_text_hover_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'home_section_cta_button_text_color')) {
//                $table->text('home_section_cta_button_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'about_section_cta_button_background_color')) {
//                $table->text('about_section_cta_button_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'about_section_cta_button_hover_background_color')) {
//                $table->text('about_section_cta_button_hover_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'about_section_cta_button_border_color')) {
//                $table->text('about_section_cta_button_border_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'about_section_cta_button_text_hover_color')) {
//                $table->text('about_section_cta_button_text_hover_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'about_section_cta_button_text_color')) {
//                $table->text('about_section_cta_button_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features1_section_cta_button_background_color')) {
//                $table->text('features1_section_cta_button_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features1_section_cta_button_hover_background_color')) {
//                $table->text('features1_section_cta_button_hover_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features1_section_cta_button_border_color')) {
//                $table->text('features1_section_cta_button_border_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features1_section_cta_button_text_hover_color')) {
//                $table->text('features1_section_cta_button_text_hover_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features1_section_cta_button_text_color')) {
//                $table->text('features1_section_cta_button_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features2_section_cta_button_background_color')) {
//                $table->text('features2_section_cta_button_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features2_section_cta_button_hover_background_color')) {
//                $table->text('features2_section_cta_button_hover_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features2_section_cta_button_border_color')) {
//                $table->text('features2_section_cta_button_border_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features2_section_cta_button_text_hover_color')) {
//                $table->text('features2_section_cta_button_text_hover_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'features2_section_cta_button_text_color')) {
//                $table->text('features2_section_cta_button_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'top_bar_background_color')) {
//                $table->text('top_bar_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'top_bar_text_color')) {
//                $table->text('top_bar_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'top_bar_dark_background_color')) {
//                $table->text('top_bar_dark_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'top_bar_dark_text_color')) {
//                $table->text('top_bar_dark_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'counter_section_background_color')) {
//                $table->text('counter_section_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'counter_section_counter_color')) {
//                $table->text('counter_section_counter_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'counter_section_cta_button_color')) {
//                $table->text('counter_section_cta_button_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'counter_section_cta_button_text_color')) {
//                $table->text('counter_section_cta_button_text_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'product_details_background_color')) {
//                $table->text('product_details_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'product_details_title_color')) {
//                $table->text('product_details_title_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'product_details_description_color')) {
//                $table->text('product_details_description_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'product_details_price_color')) {
//                $table->text('product_details_price_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'product_details_old_price_color')) {
//                $table->text('product_details_old_price_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'product_details_feature_background_color')) {
//                $table->text('product_details_feature_background_color');
//            }
//            if (!Schema::hasColumn('website_settings', 'product_details_feature_text_color')) {
//                $table->text('product_details_feature_text_color');
//            }
//        });
    }
};
