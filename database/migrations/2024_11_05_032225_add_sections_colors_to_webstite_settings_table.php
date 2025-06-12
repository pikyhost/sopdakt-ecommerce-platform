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

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webstite_settings', function (Blueprint $table) {
            $table->dropColumn([
                'feature3_section_background_color',
                'feature3_section_box_background_color',
                'feature3_section_box_title_color',
                'feature3_section_box_subtitle_color',
                'deal_of_the_week_section_background_color',
                'deal_of_the_week_section_box_background_color',
                'deal_of_the_week_section_box_title_color',
                'deal_of_the_week_section_box_subtitle_color',
                'deal_of_the_week_section_box_price_color',
                'products_section_background_color',
                'products_section_item_background_color',
                'products_section_item_image_background_color',
                'products_section_item_title_color',
                'why_choose_us_section_background_color',
                'compare_section_background_color',
                'compare_section_product_title_color',
                'compare_section_product_subtitle_color',
                'compare_section_table_property_title_color',
                'feedbacks_section_background_color',
                'feedbacks_section_comment_color',
                'feedbacks_section_client_name_color',
                'feedbacks_section_client_position_color',
                'feedbacks_section_box_background_color',
                'faq_section_question_background_color',
                'faq_section_question_color',
                'faq_section_answer_background_color',
                'faq_section_answer_color',
                'contact_section_background_color',
                'contact_section_boxes_text_color',
                'contact_section_box_background_color',
                'contact_section_form_input_field_background_color',
                'contact_section_form_input_field_text_color',
                'footer_section_background_color',
                'footer_section_subtitle_color',
            ]);
        });
    }
};
