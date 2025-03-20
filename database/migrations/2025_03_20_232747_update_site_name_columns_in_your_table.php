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
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['site_name_en', 'site_name_ar']); // Remove old columns
            $table->string('site_name')->after('id'); // Add new column
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('site_name_en')->nullable();
            $table->string('site_name_ar')->nullable();
            $table->dropColumn('site_name');
        });
    }
};
