<?php

use App\Models\Popup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            $table->boolean('is_join_us')->default(false);
        });

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
            'duration_seconds' => 30,
            'dont_show_again_days' => 14,
            'show_interval_minutes' => 90,
            'display_rules' => 'all_pages',
            'popup_order' => 1,
            'is_active' => true,
            'email_needed' => true,
            'is_join_us' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            //
        });
    }
};
