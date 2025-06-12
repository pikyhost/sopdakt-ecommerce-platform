<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            // Arabic-speaking countries (22 Arab League members)
            ['code' => 'DZ', 'en' => 'Algeria', 'ar' => 'الجزائر'],
            ['code' => 'BH', 'en' => 'Bahrain', 'ar' => 'البحرين'],
            ['code' => 'DJ', 'en' => 'Djibouti', 'ar' => 'جيبوتي'],
            ['code' => 'EG', 'en' => 'Egypt', 'ar' => 'مصر'],
            ['code' => 'IQ', 'en' => 'Iraq', 'ar' => 'العراق'],
            ['code' => 'JO', 'en' => 'Jordan', 'ar' => 'الأردن'],
            ['code' => 'KW', 'en' => 'Kuwait', 'ar' => 'الكويت'],
            ['code' => 'LB', 'en' => 'Lebanon', 'ar' => 'لبنان'],
            ['code' => 'LY', 'en' => 'Libya', 'ar' => 'ليبيا'],
            ['code' => 'MR', 'en' => 'Mauritania', 'ar' => 'موريتانيا'],
            ['code' => 'MA', 'en' => 'Morocco', 'ar' => 'المغرب'],
            ['code' => 'OM', 'en' => 'Oman', 'ar' => 'عمان'],
            ['code' => 'PS', 'en' => 'Palestine', 'ar' => 'فلسطين'],
            ['code' => 'QA', 'en' => 'Qatar', 'ar' => 'قطر'],
            ['code' => 'SA', 'en' => 'Saudi Arabia', 'ar' => 'السعودية'],
            ['code' => 'SO', 'en' => 'Somalia', 'ar' => 'الصومال'],
            ['code' => 'SD', 'en' => 'Sudan', 'ar' => 'السودان'],
            ['code' => 'SY', 'en' => 'Syria', 'ar' => 'سوريا'],
            ['code' => 'TN', 'en' => 'Tunisia', 'ar' => 'تونس'],
            ['code' => 'AE', 'en' => 'United Arab Emirates', 'ar' => 'الإمارات'],
            ['code' => 'YE', 'en' => 'Yemen', 'ar' => 'اليمن'],

            // Other major world countries
            ['code' => 'US', 'en' => 'United States', 'ar' => 'الولايات المتحدة'],
            ['code' => 'GB', 'en' => 'England', 'ar' => 'إنجلترا'],
            ['code' => 'CA', 'en' => 'Canada', 'ar' => 'كندا'],
            ['code' => 'FR', 'en' => 'France', 'ar' => 'فرنسا'],
            ['code' => 'DE', 'en' => 'Germany', 'ar' => 'ألمانيا'],
            ['code' => 'CN', 'en' => 'China', 'ar' => 'الصين'],
            ['code' => 'IN', 'en' => 'India', 'ar' => 'الهند'],
            ['code' => 'JP', 'en' => 'Japan', 'ar' => 'اليابان'],
            ['code' => 'RU', 'en' => 'Russia', 'ar' => 'روسيا'],
            ['code' => 'BR', 'en' => 'Brazil', 'ar' => 'البرازيل'],
            ['code' => 'AU', 'en' => 'Australia', 'ar' => 'أستراليا'],
            ['code' => 'ZA', 'en' => 'South Africa', 'ar' => 'جنوب أفريقيا'],
            ['code' => 'KR', 'en' => 'South Korea', 'ar' => 'كوريا الجنوبية'],
            ['code' => 'ES', 'en' => 'Spain', 'ar' => 'إسبانيا'],
            ['code' => 'IT', 'en' => 'Italy', 'ar' => 'إيطاليا'],
            ['code' => 'TR', 'en' => 'Turkey', 'ar' => 'تركيا'],
            ['code' => 'MX', 'en' => 'Mexico', 'ar' => 'المكسيك'],
            ['code' => 'ID', 'en' => 'Indonesia', 'ar' => 'إندونيسيا'],
            ['code' => 'NG', 'en' => 'Nigeria', 'ar' => 'نيجيريا'],
            ['code' => 'PK', 'en' => 'Pakistan', 'ar' => 'باكستان'],

            // Additional countries from different regions
            ['code' => 'AR', 'en' => 'Argentina', 'ar' => 'الأرجنتين'],
            ['code' => 'BD', 'en' => 'Bangladesh', 'ar' => 'بنغلاديش'],
            ['code' => 'BE', 'en' => 'Belgium', 'ar' => 'بلجيكا'],
            ['code' => 'CL', 'en' => 'Chile', 'ar' => 'تشيلي'],
            ['code' => 'CO', 'en' => 'Colombia', 'ar' => 'كولومبيا'],
            ['code' => 'CU', 'en' => 'Cuba', 'ar' => 'كوبا'],
            ['code' => 'CZ', 'en' => 'Czech Republic', 'ar' => 'جمهورية التشيك'],
            ['code' => 'DK', 'en' => 'Denmark', 'ar' => 'الدنمارك'],
            ['code' => 'ET', 'en' => 'Ethiopia', 'ar' => 'إثيوبيا'],
            ['code' => 'FI', 'en' => 'Finland', 'ar' => 'فنلندا'],
            ['code' => 'GR', 'en' => 'Greece', 'ar' => 'اليونان'],
            ['code' => 'HK', 'en' => 'Hong Kong', 'ar' => 'هونغ كونغ'],
            ['code' => 'HU', 'en' => 'Hungary', 'ar' => 'المجر'],
            ['code' => 'IE', 'en' => 'Ireland', 'ar' => 'أيرلندا'],
            ['code' => 'IL', 'en' => 'Israel', 'ar' => 'إسرائيل'],
            ['code' => 'KE', 'en' => 'Kenya', 'ar' => 'كينيا'],
            ['code' => 'MY', 'en' => 'Malaysia', 'ar' => 'ماليزيا'],
            ['code' => 'NL', 'en' => 'Netherlands', 'ar' => 'هولندا'],
            ['code' => 'NO', 'en' => 'Norway', 'ar' => 'النرويج'],
            ['code' => 'NZ', 'en' => 'New Zealand', 'ar' => 'نيوزيلندا'],
            ['code' => 'PE', 'en' => 'Peru', 'ar' => 'بيرو'],
            ['code' => 'PH', 'en' => 'Philippines', 'ar' => 'الفلبين'],
            ['code' => 'PL', 'en' => 'Poland', 'ar' => 'بولندا'],
            ['code' => 'PT', 'en' => 'Portugal', 'ar' => 'البرتغال'],
            ['code' => 'SE', 'en' => 'Sweden', 'ar' => 'السويد'],
            ['code' => 'SG', 'en' => 'Singapore', 'ar' => 'سنغافورة'],
            ['code' => 'TH', 'en' => 'Thailand', 'ar' => 'تايلاند'],
            ['code' => 'VE', 'en' => 'Venezuela', 'ar' => 'فنزويلا'],
            ['code' => 'VN', 'en' => 'Vietnam', 'ar' => 'فيتنام'],
            ['code' => 'CH', 'en' => 'Switzerland', 'ar' => 'سويسرا'],
            ['code' => 'AT', 'en' => 'Austria', 'ar' => 'النمسا'],
            ['code' => 'BY', 'en' => 'Belarus', 'ar' => 'بيلاروسيا'],
            ['code' => 'BG', 'en' => 'Bulgaria', 'ar' => 'بلغاريا'],
            ['code' => 'HR', 'en' => 'Croatia', 'ar' => 'كرواتيا'],
            ['code' => 'EE', 'en' => 'Estonia', 'ar' => 'إستونيا'],
            ['code' => 'IS', 'en' => 'Iceland', 'ar' => 'آيسلندا'],
            ['code' => 'LV', 'en' => 'Latvia', 'ar' => 'لاتفيا'],
            ['code' => 'LT', 'en' => 'Lithuania', 'ar' => 'ليتوانيا'],
            ['code' => 'LU', 'en' => 'Luxembourg', 'ar' => 'لوكسمبورغ'],
            ['code' => 'MT', 'en' => 'Malta', 'ar' => 'مالطا'],
            ['code' => 'RO', 'en' => 'Romania', 'ar' => 'رومانيا'],
            ['code' => 'RS', 'en' => 'Serbia', 'ar' => 'صربيا'],
            ['code' => 'SK', 'en' => 'Slovakia', 'ar' => 'سلوفاكيا'],
            ['code' => 'SI', 'en' => 'Slovenia', 'ar' => 'سلوفينيا'],
            ['code' => 'UA', 'en' => 'Ukraine', 'ar' => 'أوكرانيا'],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                ['name' => ['en' => $country['en'], 'ar' => $country['ar']]]
            );
        }
    }
}
