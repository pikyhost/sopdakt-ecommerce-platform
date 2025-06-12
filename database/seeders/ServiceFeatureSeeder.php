<?php

namespace Database\Seeders;

use App\Models\ServiceFeature;
use Illuminate\Database\Seeder;

class ServiceFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            [
                'title' => ['en' => 'Customer Support', 'ar' => 'دعم العملاء'],
                'subtitle' => ['en' => 'Need Assistance?', 'ar' => 'هل تحتاج إلى مساعدة؟'],
                'icon' => 'support-icon.svg',
            ],
            [
                'title' => ['en' => 'Secured Payment', 'ar' => 'دفع آمن'],
                'subtitle' => ['en' => 'Safe & Fast', 'ar' => 'آمن وسريع'],
                'icon' => 'payment-icon.svg',
            ],
            [
                'title' => ['en' => 'Free Returns', 'ar' => 'إرجاع مجاني'],
                'subtitle' => ['en' => 'Easy & Free', 'ar' => 'سهل ومجاني'],
                'icon' => 'returns-icon.svg',
            ],
            [
                'title' => ['en' => 'Free Shipping', 'ar' => 'شحن مجاني'],
                'subtitle' => ['en' => 'Made To Help You', 'ar' => 'مصمم لمساعدتك'],
                'icon' => 'shipping-icon.svg',
            ],
        ];

        foreach ($features as $feature) {
            ServiceFeature::create($feature);
        }
    }
}
