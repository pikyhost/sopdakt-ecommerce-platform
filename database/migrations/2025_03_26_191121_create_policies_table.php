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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->longText('privacy_policy_en');  // Privacy Policy (English)
            $table->longText('privacy_policy_ar');  // Privacy Policy (Arabic)
            $table->longText('refund_policy_en');   // Refund Policy (English)
            $table->longText('refund_policy_ar');   // Refund Policy (Arabic)
            $table->longText('terms_of_service_en');// Terms of Service (English)
            $table->longText('terms_of_service_ar');// Terms of Service (Arabic)
            $table->timestamps();
        });

        Policy::updateOrCreate(
            ['id' => 1], // Ensures only one record exists
            [
                'privacy_policy_en' => '# Privacy Policy in English',
                'privacy_policy_ar' => '# سياسة الخصوصية بالعربية',
                'refund_policy_en' => '# Refund Policy in English',
                'refund_policy_ar' => '# سياسة الاسترجاع بالعربية',
                'terms_of_service_en' => '# Terms of Service in English',
                'terms_of_service_ar' => '# شروط الخدمة بالعربية',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
