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
            $table->string('home_section_top_image')->nullable();
            $table->string('home_section_bottom_image')->nullable();
            $table->boolean('is_home_section_top_image')->default(0);
            $table->boolean('is_home_section_bottom_image')->default(0);

            $table->string('about_section_top_image')->nullable();
            $table->string('about_section_bottom_image')->nullable();
            $table->boolean('is_about_section_top_image')->default(0);
            $table->boolean('is_about_section_bottom_image')->default(0);

            $table->string('features1_section_top_image')->nullable();
            $table->string('features1_section_bottom_image')->nullable();
            $table->boolean('is_features1_section_top_image')->default(0);
            $table->boolean('is_features1_section_bottom_image')->default(0);

            $table->string('features2_section_top_image')->nullable();
            $table->string('features2_section_bottom_image')->nullable();
            $table->boolean('is_features2_section_top_image')->default(0);
            $table->boolean('is_features2_section_bottom_image')->default(0);

            $table->string('features3_section_top_image')->nullable();
            $table->string('features3_section_bottom_image')->nullable();
            $table->boolean('is_features3_section_top_image')->default(0);
            $table->boolean('is_features3_section_bottom_image')->default(0);

            $table->string('deal_of_the_week_section_top_image')->nullable();
            $table->string('deal_of_the_week_section_bottom_image')->nullable();
            $table->boolean('is_deal_of_the_week_section_top_image')->default(0);
            $table->boolean('is_deal_of_the_week_section_bottom_image')->default(0);

            $table->string('products_section_top_image')->nullable();
            $table->string('products_section_bottom_image')->nullable();
            $table->boolean('is_products_section_top_image')->default(0);
            $table->boolean('is_products_section_bottom_image')->default(0);

            $table->string('why_choose_us_section_top_image')->nullable();
            $table->string('why_choose_us_section_bottom_image')->nullable();
            $table->boolean('is_why_choose_us_section_top_image')->default(0);
            $table->boolean('is_why_choose_us_section_bottom_image')->default(0);

            $table->string('compares_section_top_image')->nullable();
            $table->string('compares_section_bottom_image')->nullable();
            $table->boolean('is_compares_section_top_image')->default(0);
            $table->boolean('is_compares_section_bottom_image')->default(0);

            $table->string('feedbacks_section_top_image')->nullable();
            $table->string('feedbacks_section_bottom_image')->nullable();
            $table->boolean('is_feedbacks_section_top_image')->default(0);
            $table->boolean('is_feedbacks_section_bottom_image')->default(0);

            $table->string('faq_section_top_image')->nullable();
            $table->string('faq_section_bottom_image')->nullable();
            $table->boolean('is_faq_section_top_image')->default(0);
            $table->boolean('is_faq_section_bottom_image')->default(0);

            $table->string('contact_us_section_top_image')->nullable();
            $table->string('contact_us_section_bottom_image')->nullable();
            $table->boolean('is_contact_us_section_top_image')->default(0);
            $table->boolean('is_contact_us_section_bottom_image')->default(0);

            $table->string('footer_section_top_image')->nullable();
            $table->string('footer_section_bottom_image')->nullable();
            $table->boolean('is_footer_section_top_image')->default(0);
            $table->boolean('is_footer_section_bottom_image')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn('home_section_top_image');
            $table->dropColumn('home_section_bottom_image');
            $table->dropColumn('is_home_section_top_image');
            $table->dropColumn('is_home_section_bottom_image');

            $table->dropColumn('about_section_top_image');
            $table->dropColumn('about_section_bottom_image');
            $table->dropColumn('is_about_section_top_image');
            $table->dropColumn('is_about_section_bottom_image');

            $table->dropColumn('features1_section_top_image');
            $table->dropColumn('features1_section_bottom_image');
            $table->dropColumn('is_features1_section_top_image');
            $table->dropColumn('is_features1_section_bottom_image');

            $table->dropColumn('features2_section_top_image');
            $table->dropColumn('features2_section_bottom_image');
            $table->dropColumn('is_features2_section_top_image');
            $table->dropColumn('is_features2_section_bottom_image');

            $table->dropColumn('features3_section_top_image');
            $table->dropColumn('features3_section_bottom_image');
            $table->dropColumn('is_features3_section_top_image');
            $table->dropColumn('is_features3_section_bottom_image');

            $table->dropColumn('deal_of_the_week_section_top_image');
            $table->dropColumn('deal_of_the_week_section_bottom_image');
            $table->dropColumn('is_deal_of_the_week_section_top_image');
            $table->dropColumn('is_deal_of_the_week_section_bottom_image');

            $table->dropColumn('products_section_top_image');
            $table->dropColumn('products_section_bottom_image');
            $table->dropColumn('is_products_section_top_image');
            $table->dropColumn('is_products_section_bottom_image');

            $table->dropColumn('why_choose_us_section_top_image');
            $table->dropColumn('why_choose_us_section_bottom_image');
            $table->dropColumn('is_why_choose_us_section_top_image');
            $table->dropColumn('is_why_choose_us_section_bottom_image');

            $table->dropColumn('compares_section_top_image');
            $table->dropColumn('compares_section_bottom_image');
            $table->dropColumn('is_compares_section_top_image');
            $table->dropColumn('is_compares_section_bottom_image');

            $table->dropColumn('feedbacks_section_top_image');
            $table->dropColumn('feedbacks_section_bottom_image');
            $table->dropColumn('is_feedbacks_section_top_image');
            $table->dropColumn('is_feedbacks_section_bottom_image');

            $table->dropColumn('faq_section_top_image');
            $table->dropColumn('faq_section_bottom_image');
            $table->dropColumn('is_faq_section_top_image');
            $table->dropColumn('is_faq_section_bottom_image');

            $table->dropColumn('contact_us_section_top_image');
            $table->dropColumn('contact_us_section_bottom_image');
            $table->dropColumn('is_contact_us_section_top_image');
            $table->dropColumn('is_contact_us_section_bottom_image');

            $table->dropColumn('footer_section_top_image');
            $table->dropColumn('footer_section_bottom_image');
            $table->dropColumn('is_footer_section_top_image');
            $table->dropColumn('is_footer_section_bottom_image');
        });
    }
};
