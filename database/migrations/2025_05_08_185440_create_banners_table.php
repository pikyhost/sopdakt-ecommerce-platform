<?php

use App\Models\Banner;
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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->json('discount')->nullable();
            $table->json('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('image');
            $table->enum('type', ['product', 'category']); // Added enum column
            $table->timestamps();
        });

        // Insert default banners
        Banner::create([
            'title' => ['en' => '50% OFF', 'ar' => 'خصم 50%'],
            'subtitle' => ['en' => 'UP TO', 'ar' => 'حتى'],
            'discount' => ['en' => '50%', 'ar' => '٥٠٪'],
            'button_text' => ['en' => 'SHOP NOW', 'ar' => 'تسوق الآن'],
            'button_url' => '/shop',
            'image' => 'assets/images/menu-banner.jpg',
            'type' => 'product',
        ]);

        Banner::create([
            'title' => ['en' => 'New Arrivals', 'ar' => 'وصل حديثاً'],
            'subtitle' => ['en' => 'EXPLORE', 'ar' => 'استكشاف'],
            'discount' => null,
            'button_text' => ['en' => 'VIEW CATEGORIES', 'ar' => 'عرض الفئات'],
            'button_url' => '/categories',
            'image' => 'assets/images/category-banner.jpg',
            'type' => 'category',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
