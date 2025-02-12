<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            ['en' => 'Extra Small', 'ar' => 'صغير جداً'],
            ['en' => 'Small', 'ar' => 'صغير'],
            ['en' => 'Medium', 'ar' => 'متوسط'],
            ['en' => 'Large', 'ar' => 'كبير'],
            ['en' => 'Extra Large', 'ar' => 'كبير جداً'],
            ['en' => 'Double XL', 'ar' => 'ضعف كبير جداً'],
            ['en' => 'Triple XL', 'ar' => 'ثلاثة أضعاف كبير جداً'],
        ];

        foreach ($sizes as $size) {
            Size::create([
                'name' => ['en' => $size['en'], 'ar' => $size['ar']], // Spatie Translatable
                'is_active' => true,
            ]);
        }
    }
}
