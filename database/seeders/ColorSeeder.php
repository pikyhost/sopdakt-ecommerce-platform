<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['en' => 'Red', 'ar' => 'أحمر', 'code' => '#FF0000'],
            ['en' => 'Blue', 'ar' => 'أزرق', 'code' => '#0000FF'],
            ['en' => 'Green', 'ar' => 'أخضر', 'code' => '#008000'],
            ['en' => 'Yellow', 'ar' => 'أصفر', 'code' => '#FFFF00'],
            ['en' => 'Black', 'ar' => 'أسود', 'code' => '#000000'],
            ['en' => 'White', 'ar' => 'أبيض', 'code' => '#FFFFFF'],
            ['en' => 'Purple', 'ar' => 'أرجواني', 'code' => '#800080'],
            ['en' => 'Orange', 'ar' => 'برتقالي', 'code' => '#FFA500'],
            ['en' => 'Pink', 'ar' => 'وردي', 'code' => '#FFC0CB'],
            ['en' => 'Brown', 'ar' => 'بني', 'code' => '#A52A2A'],
        ];

        foreach ($colors as $color) {
            Color::create([
                'name' => ['en' => $color['en'], 'ar' => $color['ar']], // Spatie Translatable
                'code' => $color['code'],
                'is_active' => true,
            ]);
        }
    }
}
