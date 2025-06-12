<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('top_notices', function (Blueprint $table) {
            $table->id();
            $table->text('content_en'); // Full content in English
            $table->text('content_ar'); // Full content in Arabic
            $table->string('cta_text_en')->nullable(); // Button 1 text (English)
            $table->string('cta_text_ar')->nullable(); // Button 1 text (Arabic)
            $table->string('cta_url')->nullable(); // Button 1 URL
            $table->string('cta_text_2_en')->nullable(); // Button 2 text (English)
            $table->string('cta_text_2_ar')->nullable(); // Button 2 text (Arabic)
            $table->string('cta_url_2')->nullable(); // Button 2 URL
            $table->string('limited_time_text_en')->nullable(); // Small notice text (English)
            $table->string('limited_time_text_ar')->nullable(); // Small notice text (Arabic)
            $table->boolean('is_active')->default(true); // Show or hide the top notice
            $table->timestamps();
        });

        // Insert a default record to prevent errors when accessing the data
        DB::table('top_notices')->insert([
            'content_en' => 'Get Up to <b>40% OFF</b> New-Season Styles',
            'content_ar' => 'احصل على خصم يصل إلى <b>40٪</b> على أنماط الموسم الجديد',
            'cta_text_en' => 'Shop Now',
            'cta_text_ar' => 'تسوق الآن',
            'cta_url' => '/shop',
            'cta_text_2_en' => 'Learn More',
            'cta_text_2_ar' => 'اعرف أكثر',
            'cta_url_2' => '/about',
            'limited_time_text_en' => '* Limited time only.',
            'limited_time_text_ar' => '* لفترة محدودة فقط.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('top_notices');
    }
};
