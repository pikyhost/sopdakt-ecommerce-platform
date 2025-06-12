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
        Schema::create('home_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('main_heading')->default('Spring / Summer Season');
            $table->string('discount_text')->default('Up to');
            $table->string('discount_value')->default('50% off');
            $table->decimal('starting_price', 8, 2)->default(19.99);
            $table->string('currency_symbol')->default('$');
            $table->string('button_text')->default('Shop Now');
            $table->string('button_url')->default('#');

            $table->string('center_main_heading')->default('Discover Our Collection');
            $table->string('center_button_text')->default('Explore Now');
            $table->string('center_button_url')->default('#');

            $table->string('last1_heading')->default('Summer Sale');
            $table->string('last1_subheading')->default('Get 20% Off');
            $table->string('last1_button_text')->default('Shop Now');
            $table->string('last1_button_url')->default('#');

            $table->string('last2_heading')->default('Flash Sale');
            $table->string('last2_subheading')->default('Get 30% Off');
            $table->string('last2_button_text')->default('Shop Now');
            $table->string('last2_button_url')->default('#');

            $table->string('latest_heading')->default('Explore the Best of You');
            $table->string('latest_button_text')->default('Shop Now');
            $table->string('latest_button_url')->default('#');

            $table->timestamps();
        });

        \App\Models\HomePageSetting::create([
            'main_heading' => 'Exclusive Spring Deals',
            'discount_text' => 'Save Up To',
            'discount_value' => '40% off',
            'starting_price' => 29.99,
            'currency_symbol' => '€',
            'button_text' => 'Discover More',
            'button_url' => '/spring-collection',

            'center_main_heading' => 'New Arrivals Just for You',
            'center_button_text' => 'Shop Now',
            'center_button_url' => '/new-arrivals',

            'last1_heading' => 'Special Discount',
            'last1_subheading' => 'Enjoy 15% Off',
            'last1_button_text' => 'Grab Now',
            'last1_button_url' => '/discount-offer',

            'last2_heading' => 'Limited-Time Offers',
            'last2_subheading' => 'Exclusive 25% Discount',
            'last2_button_text' => 'Check Now',
            'last2_button_url' => '/limited-deals',

            'latest_heading' => 'Top Picks for You',
            'latest_button_text' => 'Browse Collection',
            'latest_button_url' => '/top-picks',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_page_settings');
    }
};
