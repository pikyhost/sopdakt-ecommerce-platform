<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['en' => 'United States Dollar', 'ar' => 'الدولار الأمريكي', 'code' => 'USD', 'symbol' => '$'],
            ['en' => 'Euro', 'ar' => 'اليورو', 'code' => 'EUR', 'symbol' => '€'],
            ['en' => 'British Pound Sterling', 'ar' => 'الجنيه الاسترليني', 'code' => 'GBP', 'symbol' => '£'],
            ['en' => 'Saudi Riyal', 'ar' => 'الريال السعودي', 'code' => 'SAR', 'symbol' => '﷼'],
            ['en' => 'United Arab Emirates Dirham', 'ar' => 'الدرهم الإماراتي', 'code' => 'AED', 'symbol' => 'د.إ'],
            ['en' => 'Egyptian Pound', 'ar' => 'الجنيه المصري', 'code' => 'EGP', 'symbol' => '£'],
        ];

        foreach ($currencies as $currency) {
            Currency::create([
                'code' => $currency['code'],
                'name' => ['en' => $currency['en'], 'ar' => $currency['ar']], // Spatie Translatable
                'symbol' => $currency['symbol'],
                'is_active' => true,
            ]);
        }
    }
}
