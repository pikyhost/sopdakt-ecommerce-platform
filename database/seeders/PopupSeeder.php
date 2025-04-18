<?php

namespace Database\Seeders;

use App\Models\Popup;
use Illuminate\Database\Seeder;

class PopupSeeder extends Seeder
{
    public function run(): void
    {
        // Update or create: Special Offer
        Popup::updateOrCreate(
            ['cta_link' => '/shop'],
            [
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
                'image_path' => 'popups/special_offer.jpg',
                'delay_seconds' => 5,
                'is_active' => true,
                'email_needed' => false,
                'display_rules' => 'all_pages',
                'popup_order' => 1,
                'specific_pages' => null,
                'show_interval_minutes' => 60,
                'duration_seconds' => 60,
                'dont_show_again_days' => 7,
            ]
        );

        // Update or create: Subscribe & Save
        Popup::updateOrCreate(
            ['cta_link' => '/subscribe'],
            [
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
                'image_path' => 'popups/newsletter.jpg',
                'delay_seconds' => 8,
                'is_active' => true,
                'email_needed' => true,
                'display_rules' => 'specific_pages',
                'popup_order' => 2,
                'specific_pages' => ['/home', '/products'],
                'show_interval_minutes' => 60,
                'duration_seconds' => 60,
                'dont_show_again_days' => 7,
            ]
        );

        // New: Ramadan Sale
        Popup::create([
            'title' => [
                'en' => 'Ramadan Sale',
                'ar' => 'تخفيضات رمضان',
            ],
            'description' => [
                'en' => 'Enjoy exclusive Ramadan discounts on all categories.',
                'ar' => 'استمتع بتخفيضات رمضان الحصرية على جميع الفئات.',
            ],
            'cta_text' => [
                'en' => 'Explore Deals',
                'ar' => 'استعرض العروض',
            ],
            'cta_link' => '/ramadan-sale',
            'image_path' => 'popups/ramadan.jpg',
            'delay_seconds' => 10,
            'is_active' => true,
            'email_needed' => false,
            'display_rules' => 'all_except_specific',
            'popup_order' => 3,
            'specific_pages' => ['/checkout'],
            'show_interval_minutes' => 120,
            'duration_seconds' => 45,
            'dont_show_again_days' => 5,
        ]);

        // New: Member Exclusive
        Popup::create([
            'title' => [
                'en' => 'Member Exclusive',
                'ar' => 'عرض للأعضاء فقط',
            ],
            'description' => [
                'en' => 'Login to access exclusive content and offers.',
                'ar' => 'سجّل الدخول للوصول إلى محتوى وعروض حصرية.',
            ],
            'cta_text' => [
                'en' => 'Login',
                'ar' => 'تسجيل الدخول',
            ],
            'cta_link' => '/login',
            'image_path' => 'popups/members_only.jpg',
            'delay_seconds' => 6,
            'is_active' => true,
            'email_needed' => false,
            'display_rules' => 'all_except_group',
            'popup_order' => 4,
            'specific_pages' => ['guest', 'visitors'],
            'show_interval_minutes' => 90,
            'duration_seconds' => 30,
            'dont_show_again_days' => 14,
        ]);
    }
}
