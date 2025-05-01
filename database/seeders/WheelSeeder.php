<?php

namespace Database\Seeders;

use App\Models\Wheel;
use Illuminate\Database\Seeder;

class WheelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wheels = [
            [
                'name' => [
                    'en' => 'Summer Spin Wheel',
                    'ar' => 'عجلة الدوران الصيفية'
                ],
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'spins_per_user' => 3,
                'spins_duration' => 24,
                'display_rules' => 'all_pages',
                'specific_pages' => null
            ],
            [
                'name' => [
                    'en' => 'Winter Rewards Wheel',
                    'ar' => 'عجلة المكافآت الشتوية'
                ],
                'is_active' => true,
                'start_date' => now()->addMonths(2),
                'end_date' => now()->addMonths(3),
                'spins_per_user' => 5,
                'spins_duration' => 12,
                'display_rules' => 'specific_pages',
                'specific_pages' => ['/promotions', '/offers']
            ],
            [
                'name' => [
                    'en' => 'New Year Special Wheel',
                    'ar' => 'عجلة السنة الجديدة الخاصة'
                ],
                'is_active' => false, // Inactive for now
                'start_date' => now()->addYear(),
                'end_date' => now()->addYear()->addMonth(),
                'spins_per_user' => 1,
                'spins_duration' => 48,
                'display_rules' => 'all_pages',
                'specific_pages' => null
            ]
        ];

        foreach ($wheels as $wheel) {
            Wheel::create($wheel);
        }
    }
}
