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
        Schema::table('top_notices', function (Blueprint $table) {
            $table->string('header_message_en')->nullable(); // Header message (English)
            $table->string('header_message_ar')->nullable(); // Header message (Arabic)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('top_notices', function (Blueprint $table) {
            //
        });
    }
};
