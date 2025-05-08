<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('top_notices', function (Blueprint $table) {
            $table->dropColumn([
                'cta_text_en',
                'cta_text_ar',
                'cta_url',
                'cta_text_2_en',
                'cta_text_2_ar',
                'cta_url_2',
                'limited_time_text_en',
                'limited_time_text_ar',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('top_notices', function (Blueprint $table) {
            $table->string('cta_text_en')->nullable();
            $table->string('cta_text_ar')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('cta_text_2_en')->nullable();
            $table->string('cta_text_2_ar')->nullable();
            $table->string('cta_url_2')->nullable();
            $table->string('limited_time_text_en')->nullable();
            $table->string('limited_time_text_ar')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }
};
