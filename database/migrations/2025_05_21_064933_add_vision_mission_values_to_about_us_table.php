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
            $table->string('vision_title')->nullable()->after('about_image');
            $table->string('vision_content')->nullable()->after('vision_title');
            $table->string('mission_title')->nullable()->after('vision_content');
            $table->string('mission_content')->nullable()->after('mission_title');
            $table->string('values_title')->nullable()->after('mission_content');
            $table->string('values_content')->nullable()->after('values_title');
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
