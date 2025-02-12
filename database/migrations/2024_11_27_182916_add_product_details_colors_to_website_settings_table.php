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
            $table->text('product_details_background_color')->nullable();
            $table->text('product_details_title_color')->nullable();
            $table->text('product_details_description_color')->nullable();
            $table->text('product_details_price_color')->nullable();
            $table->text('product_details_old_price_color')->nullable();
            $table->text('product_details_feature_background_color')->nullable();
            $table->text('product_details_feature_text_color')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn('product_details_background_color');
            $table->dropColumn('product_details_title_color');
            $table->dropColumn('product_details_description_color');
            $table->dropColumn('product_details_price_color');
            $table->dropColumn('product_details_old_price_color');
            $table->dropColumn('product_details_feature_background_color');
            $table->dropColumn('product_details_feature_text_color');

        });
    }
};
