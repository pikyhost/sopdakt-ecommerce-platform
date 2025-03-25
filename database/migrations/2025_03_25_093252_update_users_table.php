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
        Schema::table('users', function (Blueprint $table) {
            // Remove the foreign keys
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');

            $table->dropForeign(['governorate_id']);
            $table->dropColumn('governorate_id');

            $table->dropForeign(['city_id']);
            $table->dropColumn('city_id');

            // Add second_phone column
            $table->string('second_phone')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
