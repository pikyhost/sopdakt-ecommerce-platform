<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Governorate;

class CitiesSeeder extends Seeder
{
    public function run(): void
    {
        // Define cities for each governorate
        $cities = [
            'Cairo' => [
                ['en' => 'Downtown Cairo', 'ar' => 'وسط البلد'],
                ['en' => 'Nasr City', 'ar' => 'مدينة نصر'],
                ['en' => 'Heliopolis', 'ar' => 'مصر الجديدة'],
                ['en' => 'Maadi', 'ar' => 'المعادي'],
                ['en' => 'Zamalek', 'ar' => 'الزمالك'],
            ],
            'Alexandria' => [
                ['en' => 'Montaza', 'ar' => 'المنتزه'],
                ['en' => 'Smouha', 'ar' => 'سموحة'],
                ['en' => 'Roushdy', 'ar' => 'روضى'],
                ['en' => 'Sidi Gaber', 'ar' => 'سيدي جابر'],
            ],
            'Giza' => [
                ['en' => 'Dokki', 'ar' => 'الدقي'],
                ['en' => 'Mohandessin', 'ar' => 'المهندسين'],
                ['en' => '6th of October City', 'ar' => 'مدينة السادس من أكتوبر'],
                ['en' => 'Sheikh Zayed City', 'ar' => 'مدينة الشيخ زايد'],
            ],
            'Abu Dhabi' => [
                ['en' => 'Abu Dhabi City', 'ar' => 'مدينة أبوظبي'],
                ['en' => 'Al Ain', 'ar' => 'العين'],
                ['en' => 'Al Dhafra', 'ar' => 'الظفرة'],
            ],
            'Dubai' => [
                ['en' => 'Dubai City', 'ar' => 'مدينة دبي'],
                ['en' => 'Jumeirah', 'ar' => 'جميرا'],
                ['en' => 'Deira', 'ar' => 'ديرة'],
                ['en' => 'Bur Dubai', 'ar' => 'بر دبي'],
            ],
            'Riyadh' => [
                ['en' => 'Riyadh City', 'ar' => 'مدينة الرياض'],
                ['en' => 'Diriyah', 'ar' => 'الدرعية'],
                ['en' => 'Al Kharj', 'ar' => 'الخرج'],
            ],
            'Greater London' => [
                ['en' => 'London City', 'ar' => 'مدينة لندن'],
                ['en' => 'Westminster', 'ar' => 'ويستمنستر'],
                ['en' => 'Camden', 'ar' => 'كامدن'],
                ['en' => 'Kensington', 'ar' => 'كينسينغتون'],
            ],
        ];

        // Loop through the cities array and seed them
        foreach ($cities as $governorateName => $cityList) {
            // Find the governorate by name
            $governorate = Governorate::where('name->en', $governorateName)->first();

            if ($governorate) {
                foreach ($cityList as $city) {
                    City::updateOrCreate(
                        ['name' => $city, 'governorate_id' => $governorate->id],
                        ['name' => $city]
                    );
                }
            }
        }
    }
}
