<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => ['en' => 'Smartphone1', 'ar' => 'هاتف ذكي'],
                'description' => ['en' => 'Latest model with high-speed processor', 'ar' => 'أحدث طراز بمعالج عالي السرعة'],
                'sku' => 'SM-0011',
                'price' => 799.99,
                'after_discount_price' => 699.99,
                'discount_start' => now()->subDays(2),
                'discount_end' => now()->addDays(5),
                'meta_title' => ['en' => 'Smartphone', 'ar' => 'هاتف ذكي'],
                'meta_description' => ['en' => 'Buy the latest smartphone now.', 'ar' => 'اشترِ أحدث هاتف ذكي الآن'],
                'is_published' => true,
                'is_featured' => true,
            ],
            [
                'name' => ['en' => 'Laptop1', 'ar' => 'حاسوب محمول'],
                'description' => ['en' => 'Powerful laptop for gaming and work', 'ar' => 'حاسوب محمول قوي للألعاب والعمل'],
                'sku' => 'LT-0022',
                'price' => 1299.99,
                'after_discount_price' => 1199.99,
                'discount_start' => now()->subDays(1),
                'discount_end' => now()->addDays(7),
                'meta_title' => ['en' => 'Laptop', 'ar' => 'حاسوب محمول'],
                'meta_description' => ['en' => 'High-performance laptop available.', 'ar' => 'حاسوب محمول عالي الأداء متاح'],
                'is_published' => true,
                'is_featured' => false,
            ],
            [
                'name' => ['en' => 'Wireless Headphones1', 'ar' => 'سماعات لاسلكية'],
                'description' => ['en' => 'Noise-canceling headphones with long battery life', 'ar' => 'سماعات عازلة للضوضاء بعمر بطارية طويل'],
                'sku' => 'WH-0033',
                'price' => 199.99,
                'after_discount_price' => 149.99,
                'discount_start' => now()->subDays(3),
                'discount_end' => now()->addDays(4),
                'meta_title' => ['en' => 'Wireless Headphones', 'ar' => 'سماعات لاسلكية'],
                'meta_description' => ['en' => 'Experience immersive sound quality.', 'ar' => 'استمتع بجودة صوت غامرة'],
                'is_published' => true,
                'is_featured' => true,
            ],
        ];

        // Assign products to categories and users
        $category = Category::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();

        // Inside the foreach loop
        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'description' => $product['description'],
                'sku' => $product['sku'],
                'quantity' => 1,
                'price' => $product['price'],
                'after_discount_price' => $product['after_discount_price'],
                'discount_start' => $product['discount_start'],
                'discount_end' => $product['discount_end'],
                'meta_title' => $product['meta_title'],
                'meta_description' => $product['meta_description'],
                'is_published' => $product['is_published'],
                'is_featured' => $product['is_featured'],
                'slug' => Str::slug($product['name']['en']), // ✅ Generate slug from English name
                'category_id' => $category?->id,
                'user_id' => $user?->id,
            ]);
        }

        $this->command->info('Products seeded successfully.');
    }
}
