<?php

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
        Schema::table('about_us', function (Blueprint $table) {
            $table->string('founder_label')->nullable()->after('feature_image_four');
            $table->string('founder_name')->nullable()->after('founder_label');
            $table->string('founder_title')->nullable()->after('founder_name');
            $table->string('founder_image')->nullable()->after('founder_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('about_us', function (Blueprint $table) {
            //
        });
    }
};
