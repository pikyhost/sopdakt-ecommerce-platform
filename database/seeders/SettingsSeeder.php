<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Combine all settings into one JSON structure
        $settings = [
            'logo' => [
                'en' => 'images/logo-en.png',
                'ar' => 'images/logo-ar.png',
            ],
        ];

        // Store the combined settings in a single record
        Setting::updateOrCreate(
            ['key' => 'site_settings'],
            ['value' => json_encode($settings)]
        );
    }
}
