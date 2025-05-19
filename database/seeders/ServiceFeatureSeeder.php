<?php

namespace Database\Seeders;

use App\Models\ServiceFeature;
use Illuminate\Database\Seeder;

class ServiceFeatureSeeder extends Seeder
{
    public function run(): void
    {
        ServiceFeature::insert([
            [
                'title' => 'Customer Support',
                'subtitle' => 'Need Assistance?',
                'icon' => 'support-icon.svg',
            ],
            [
                'title' => 'Secured Payment',
                'subtitle' => 'Safe & Fast',
                'icon' => 'payment-icon.svg',
            ],
            [
                'title' => 'Free Returns',
                'subtitle' => 'Easy & Free',
                'icon' => 'returns-icon.svg',
            ],
            [
                'title' => 'Free Shipping',
                'subtitle' => 'Made To Help You',
                'icon' => 'shipping-icon.svg',
            ],
        ]);
    }
}
