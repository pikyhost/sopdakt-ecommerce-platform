<?php

namespace Database\Seeders;

use App\Models\Popup;
use Illuminate\Database\Seeder;

class PopupSeeder extends Seeder
{
    public function run(): void
    {
        Popup::create([
            'title' => [
                'en' => 'Special Offer!',
                'ar' => 'عرض خاص!',
            ],
            'description' => [
                'en' => 'Get 20% off your first purchase. Limited time only!',
                'ar' => 'احصل على خصم 20٪ على أول عملية شراء لك. لفترة محدودة فقط!',
            ],
            'cta_text' => [
                'en' => 'Shop Now',
                'ar' => 'تسوق الآن',
            ],
            'cta_link' => '/shop',
            'image_path' => 'popups/special_offer.jpg',
            'delay_seconds' => 5,
            'is_active' => true,
            'display_rules' => 'all_pages',
            'specific_pages' => null,
        ]);

        Popup::create([
            'title' => [
                'en' => 'Subscribe & Save',
                'ar' => 'اشترك ووفّر',
            ],
            'description' => [
                'en' => 'Join our newsletter and save 10% instantly.',
                'ar' => 'اشترك في النشرة الإخبارية واحصل على خصم 10٪ فوراً.',
            ],
            'cta_text' => [
                'en' => 'Subscribe',
                'ar' => 'اشترك',
            ],
            'cta_link' => '/subscribe',
            'image_path' => 'popups/newsletter.jpg',
            'delay_seconds' => 8,
            'is_active' => true,
            'display_rules' => 'specific_pages',
            'specific_pages' => json_encode(['/home', '/products']),
        ]);
    }
}
