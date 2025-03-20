<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingPageNavbarItems;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LandingPageNavbarItemsSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'home', 'display_name' => 'Home', 'status' => true],
            ['name' => 'about', 'display_name' => 'About', 'status' => true],
            ['name' => 'feature1', 'display_name' => 'Feature 1', 'status' => false],
            ['name' => 'feature2', 'display_name' => 'Feature 2', 'status' => false],
            ['name' => 'feature3', 'display_name' => 'Feature 3', 'status' => false],
            ['name' => 'deal_of_the_week', 'display_name' => 'Deal Of The Week', 'status' => false],
            ['name' => 'products', 'display_name' => 'Products', 'status' => false],
            ['name' => 'why_choose_us', 'display_name' => 'Why Choose Us', 'status' => true],
            ['name' => 'compare', 'display_name' => 'Compare', 'status' => true],
            ['name' => 'feedback', 'display_name' => 'Feedback', 'status' => true],
            ['name' => 'faq', 'display_name' => 'Faq', 'status' => true],
            ['name' => 'contact', 'display_name' => 'Contact', 'status' => true],
        ];

        foreach ($items as $item) {
            LandingPageNavbarItems::updateOrCreate(
                ['name' => $item['name']],
                [
                    'display_name' => $item['display_name'],
                    'status' => $item['status']
                ]
            );
        }
    }
}
