<?php

use App\Models\Policy;
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
        Schema::table('policies', function (Blueprint $table) {
            $table->longText('about_us_en')->nullable()->after('terms_of_service_ar');
            $table->longText('about_us_ar')->nullable()->after('about_us_en');
            $table->longText('contact_us_en')->nullable()->after('about_us_ar');
            $table->longText('contact_us_ar')->nullable()->after('contact_us_en');
        });

        Policy::updateOrCreate(
            ['id' => 1], // Ensures only one record exists
            [
                'about_us_en' => '# About Us in English',
                'about_us_ar' => '# من نحن بالعربية',
                'contact_us_en' => '# Contact Us in English',
                'contact_us_ar' => '# اتصل بنا بالعربية',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            //
        });
    }
};
