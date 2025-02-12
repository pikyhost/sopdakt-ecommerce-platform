<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
                'description' => ['en' => 'Devices and gadgets', 'ar' => 'أجهزة وأدوات'],
                'slug' => 'electronics',
                'meta_title' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
                'meta_description' => ['en' => 'Find the latest gadgets.', 'ar' => 'اكتشف أحدث الأدوات'],
                'is_published' => true,
            ],
            [
                'name' => ['en' => 'Clothing', 'ar' => 'ملابس'],
                'description' => ['en' => 'Fashion and apparel', 'ar' => 'الموضة والملابس'],
                'slug' => 'clothing',
                'meta_title' => ['en' => 'Clothing', 'ar' => 'ملابس'],
                'meta_description' => ['en' => 'Shop trendy outfits.', 'ar' => 'تسوق الملابس العصرية'],
                'is_published' => true,
            ],
            [
                'name' => ['en' => 'Home & Kitchen', 'ar' => 'المنزل والمطبخ'],
                'description' => ['en' => 'Furniture and appliances', 'ar' => 'الأثاث والأجهزة'],
                'slug' => 'home-kitchen',
                'meta_title' => ['en' => 'Home & Kitchen', 'ar' => 'المنزل والمطبخ'],
                'meta_description' => ['en' => 'Upgrade your home.', 'ar' => 'قم بترقية منزلك'],
                'is_published' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'], // Spatie Translatable
                'description' => $category['description'],
                'slug' => $category['slug'],
                'meta_title' => $category['meta_title'],
                'meta_description' => $category['meta_description'],
                'is_published' => $category['is_published'],
            ]);
        }

        $this->command->info('Categories seeded successfully.');
    }
}
