<?php

namespace Database\Seeders;

use App\Models\HomePageSetting;
use Illuminate\Database\Seeder;

class HomePageSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = HomePageSetting::firstOrNew(['id' => 1]);

        $setting->main_heading = [
            'en' => 'Welcome to Our Store',
            'ar' => 'مرحبًا بكم في متجرنا'
        ];
        $setting->discount_text = [
            'en' => 'Big Sale',
            'ar' => 'تخفيضات ضخمة'
        ];
        $setting->discount_value = [
            'en' => 'Up to 50%',
            'ar' => 'خصم حتى 50%'
        ];
        $setting->starting_price = '299';
        $setting->currency_symbol = '$';
        $setting->button_text = [
            'en' => 'Shop Now',
            'ar' => 'تسوق الآن'
        ];
        $setting->button_url = 'https://example.com/shop';

        $setting->center_main_heading = [
            'en' => 'Mid-Season Offers',
            'ar' => 'عروض منتصف الموسم'
        ];
        $setting->center_button_text = [
            'en' => 'Explore',
            'ar' => 'استكشف'
        ];
        $setting->center_button_url = 'https://example.com/midseason';

        $setting->last1_heading = [
            'en' => 'Gadgets',
            'ar' => 'أدوات'
        ];
        $setting->last1_subheading = [
            'en' => 'Best Tech Deals',
            'ar' => 'أفضل عروض التقنية'
        ];
        $setting->last1_button_text = [
            'en' => 'View All',
            'ar' => 'عرض الكل'
        ];
        $setting->last1_button_url = 'https://example.com/gadgets';

        $setting->last2_heading = [
            'en' => 'Accessories',
            'ar' => 'ملحقات'
        ];
        $setting->last2_subheading = [
            'en' => 'Top Picks for You',
            'ar' => 'أفضل الاختيارات لك'
        ];
        $setting->last2_button_text = [
            'en' => 'Discover',
            'ar' => 'اكتشف'
        ];
        $setting->last2_button_url = 'https://example.com/accessories';

        $setting->latest_heading = [
            'en' => 'Just Arrived',
            'ar' => 'وصل حديثًا'
        ];
        $setting->latest_button_text = [
            'en' => 'Check Now',
            'ar' => 'افحص الآن'
        ];
        $setting->latest_button_url = 'https://example.com/latest';

        $setting->save();

        // Optional: attach fake media images if using Spatie Media Library
        // $setting->addMedia(storage_path('app/public/sample.jpg'))->preservingOriginal()->toMediaCollection('slider1');

        $this->command->info('HomePageSetting seeded with fake bilingual content.');
    }
}
