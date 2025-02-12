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
        Schema::table('landing_page_deal_of_the_week_sections', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\LandingPage::class)->after('id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_page_deal_of_the_week_sections', function (Blueprint $table) {
            $table->dropForeign(['landing_page_id']);
            $table->dropColumn('landing_page_id');
        });
    }
};
