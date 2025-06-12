<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Governorate;

class GovernoratesSeeder extends Seeder
{
    public function run(): void
    {
        $governorates = [
            'EG' => [
                ['en' => 'Cairo', 'ar' => 'القاهرة'],
                ['en' => 'Alexandria', 'ar' => 'الإسكندرية'],
                ['en' => 'Giza', 'ar' => 'الجيزة'],
                ['en' => 'Sharqia', 'ar' => 'الشرقية'],
                ['en' => 'Dakahlia', 'ar' => 'الدقهلية'],
                ['en' => 'Gharbia', 'ar' => 'الغربية'],
                ['en' => 'Beheira', 'ar' => 'البحيرة'],
                ['en' => 'Fayoum', 'ar' => 'الفيوم'],
                ['en' => 'Qalyubia', 'ar' => 'القليوبية'],
                ['en' => 'Minya', 'ar' => 'المنيا'],
                ['en' => 'Asyut', 'ar' => 'أسيوط'],
                ['en' => 'Sohag', 'ar' => 'سوهاج'],
                ['en' => 'Luxor', 'ar' => 'الأقصر'],
                ['en' => 'Aswan', 'ar' => 'أسوان'],
                ['en' => 'Port Said', 'ar' => 'بورسعيد'],
                ['en' => 'Ismailia', 'ar' => 'الإسماعيلية'],
                ['en' => 'Suez', 'ar' => 'السويس'],
                ['en' => 'Red Sea', 'ar' => 'البحر الأحمر'],
                ['en' => 'New Valley', 'ar' => 'الوادي الجديد'],
                ['en' => 'Matrouh', 'ar' => 'مطروح'],
                ['en' => 'North Sinai', 'ar' => 'شمال سيناء'],
                ['en' => 'South Sinai', 'ar' => 'جنوب سيناء'],
                ['en' => 'Kafr El Sheikh', 'ar' => 'كفر الشيخ'],
                ['en' => 'Beni Suef', 'ar' => 'بني سويف'],
                ['en' => 'Qena', 'ar' => 'قنا'],
                ['en' => 'Damietta', 'ar' => 'دمياط'],
                ['en' => 'Monufia', 'ar' => 'المنوفية'],
            ],
            'AE' => [
                ['en' => 'Abu Dhabi', 'ar' => 'أبوظبي'],
                ['en' => 'Dubai', 'ar' => 'دبي'],
                ['en' => 'Sharjah', 'ar' => 'الشارقة'],
                ['en' => 'Ajman', 'ar' => 'عجمان'],
                ['en' => 'Fujairah', 'ar' => 'الفجيرة'],
                ['en' => 'Ras Al Khaimah', 'ar' => 'رأس الخيمة'],
                ['en' => 'Umm Al Quwain', 'ar' => 'أم القيوين'],
            ],
            'SA' => [
                ['en' => 'Riyadh', 'ar' => 'الرياض'],
                ['en' => 'Jeddah', 'ar' => 'جدة'],
                ['en' => 'Mecca', 'ar' => 'مكة'],
                ['en' => 'Medina', 'ar' => 'المدينة المنورة'],
                ['en' => 'Dammam', 'ar' => 'الدمام'],
                ['en' => 'Khobar', 'ar' => 'الخبر'],
                ['en' => 'Tabuk', 'ar' => 'تبوك'],
                ['en' => 'Hail', 'ar' => 'حائل'],
                ['en' => 'Najran', 'ar' => 'نجران'],
                ['en' => 'Abha', 'ar' => 'أبها'],
                ['en' => 'Jizan', 'ar' => 'جازان'],
                ['en' => 'Al Bahah', 'ar' => 'الباحة'],
                ['en' => 'Al Jawf', 'ar' => 'الجوف'],
                ['en' => 'Northern Borders', 'ar' => 'الحدود الشمالية'],
                ['en' => 'Qassim', 'ar' => 'القصيم'],
                ['en' => 'Asir', 'ar' => 'عسير'],
                ['en' => 'Al Qatif', 'ar' => 'القطيف'],
                ['en' => 'Al Kharj', 'ar' => 'الخرج'],
                ['en' => 'Al Ahsa', 'ar' => 'الأحساء'],
                ['en' => 'Taif', 'ar' => 'الطائف'],
            ],
            'GB' => [
                ['en' => 'Greater London', 'ar' => 'لندن الكبرى'],
                ['en' => 'West Midlands', 'ar' => 'ويست ميدلاندز'],
                ['en' => 'Greater Manchester', 'ar' => 'مانشستر الكبرى'],
                ['en' => 'Merseyside', 'ar' => 'ميرسيسايد'],
                ['en' => 'West Yorkshire', 'ar' => 'ويست يوركشاير'],
                ['en' => 'South Yorkshire', 'ar' => 'ساوث يوركشاير'],
                ['en' => 'Lancashire', 'ar' => 'لانكشاير'],
                ['en' => 'Surrey', 'ar' => 'سري'],
                ['en' => 'Kent', 'ar' => 'كينت'],
                ['en' => 'Essex', 'ar' => 'إسكس'],
                ['en' => 'Hampshire', 'ar' => 'هامبشاير'],
                ['en' => 'Hertfordshire', 'ar' => 'هيرتفوردشاير'],
                ['en' => 'Norfolk', 'ar' => 'نورفولك'],
                ['en' => 'Suffolk', 'ar' => 'سوفولك'],
                ['en' => 'Cheshire', 'ar' => 'تشيشير'],
                ['en' => 'Devon', 'ar' => 'ديفون'],
                ['en' => 'Cornwall', 'ar' => 'كورنوال'],
                ['en' => 'Derbyshire', 'ar' => 'ديربيشاير'],
                ['en' => 'Nottinghamshire', 'ar' => 'نوتنغهامشاير'],
                ['en' => 'Lincolnshire', 'ar' => 'لينكولنشاير'],
            ],
        ];

        foreach ($governorates as $countryCode => $governorateList) {
            $country = Country::where('code', $countryCode)->first();
            if ($country) {
                foreach ($governorateList as $governorate) {
                    Governorate::updateOrCreate(
                        ['name' => $governorate, 'country_id' => $country->id],
                        ['name' => $governorate]
                    );
                }
            }
        }
    }
}
