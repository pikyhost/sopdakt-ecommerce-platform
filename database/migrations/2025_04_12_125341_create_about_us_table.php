<?php

use App\Models\AboutUs;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();

            // Section Titles
            $table->string('about_title')->nullable();
            $table->string('team_title')->nullable();
            $table->string('testimonial_title')->nullable();
            $table->string('header_title')->nullable();
            $table->string('header_subtitle')->nullable(); // Added for more flexibility

            // Breadcrumbs
            $table->string('breadcrumb_home')->nullable();
            $table->string('breadcrumb_current')->nullable();

            // About Descriptions
            $table->text('about_description_1')->nullable();
            $table->text('about_description_2')->nullable();
            $table->string('about_image')->nullable(); // Added for about section image

            // Accordion Content (consider separate table if many items needed)
            $table->string('accordion_title_1')->nullable();
            $table->text('accordion_content_1')->nullable();
            $table->string('accordion_title_2')->nullable();
            $table->text('accordion_content_2')->nullable();
            $table->string('accordion_title_3')->nullable();
            $table->text('accordion_content_3')->nullable();
            $table->string('accordion_title_4')->nullable();
            $table->text('accordion_content_4')->nullable();

            // Team Members (consider separate table if complex team management needed)
            $table->json('team_members')->nullable();
            $table->json('team_members_ar')->nullable();

            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();

            // Testimonials (consider separate table if multiple testimonials needed)
            $table->text('testimonial_content')->nullable();
            $table->string('testimonial_name')->nullable();
            $table->string('testimonial_role')->nullable();
            $table->string('testimonial_image')->nullable();
            $table->integer('testimonial_rating')->nullable(); // Added for star ratings

            // SEO Fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();
        });

        AboutUs::updateOrCreate(['id' => 1], [
            // Translatable fields
            'about_title' => [
                'en' => 'About Us',
                'ar' => 'من نحن',
            ],
            'team_title' => [
                'en' => 'Team',
                'ar' => 'الفريق',
            ],
            'testimonial_title' => [
                'en' => 'Testimonials',
                'ar' => 'الشهادات',
            ],
            'header_title' => [
                'en' => 'Who we are',
                'ar' => 'من نحن',
            ],
            'breadcrumb_home' => [
                'en' => 'Home',
                'ar' => 'الرئيسية',
            ],
            'breadcrumb_current' => [
                'en' => 'About Us',
                'ar' => 'من نحن',
            ],
            'about_description_1' => [
                'en' => 'We are a forward-thinking company...',
                'ar' => 'نحن شركة متقدمة التفكير...',
            ],
            'about_description_2' => [
                'en' => 'Our goal is to innovate and lead...',
                'ar' => 'هدفنا هو الابتكار والريادة...',
            ],
            'accordion_title_1' => [
                'en' => 'Company History',
                'ar' => 'تاريخ الشركة',
            ],
            'accordion_content_1' => [
                'en' => 'We started in 2005 with a mission...',
                'ar' => 'بدأنا في عام 2005 مع مهمة...',
            ],
            'accordion_title_2' => [
                'en' => 'Our Vision',
                'ar' => 'رؤيتنا',
            ],
            'accordion_content_2' => [
                'en' => 'To be the best in class...',
                'ar' => 'لنكون الأفضل في فئتنا...',
            ],
            'accordion_title_3' => [
                'en' => 'Our Mission',
                'ar' => 'مهمتنا',
            ],
            'accordion_content_3' => [
                'en' => 'To deliver quality products...',
                'ar' => 'لتقديم منتجات عالية الجودة...',
            ],
            'accordion_title_4' => [
                'en' => 'Fun Facts',
                'ar' => 'حقائق ممتعة',
            ],
            'accordion_content_4' => [
                'en' => 'We’ve served over 1M users...',
                'ar' => 'لقد خدمنا أكثر من مليون مستخدم...',
            ],

            // Non-translatable fields
            'team_members' => [
                ['name' => 'John Doe', 'image' => 'team/team1.jpg'],
                ['name' => 'Jessica Doe', 'image' => 'team/team2.jpg'],
                ['name' => 'Rick Edward', 'image' => 'team/team3.jpg'],
                ['name' => 'Melinda Wolosky', 'image' => 'team/team4.jpg'],
            ],
            'team_members_ar' => [
                ['name' => 'جون دو', 'image' => 'team/team1.jpg'],
                ['name' => 'جيسيكا دو', 'image' => 'team/team2.jpg'],
                ['name' => 'ريك إدوارد', 'image' => 'team/team3.jpg'],
                ['name' => 'ميليندا وولوسكي', 'image' => 'team/team4.jpg'],
            ],
            'testimonial_content' => 'Long established fact...',
            'testimonial_name' => 'John Doe',
            'testimonial_role' => 'Porto Founder',
            'testimonial_image' => 'clients/client1.jpg',
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('about_us');
    }
};
