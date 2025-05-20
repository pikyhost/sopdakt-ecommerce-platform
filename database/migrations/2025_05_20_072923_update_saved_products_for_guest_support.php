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
        Schema::table('saved_products', function (Blueprint $table) {
            // Make user_id nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Add session_id
            $table->string('session_id')->nullable()->after('user_id')->index();

            // Drop existing unique constraint
            $table->dropUnique(['product_id', 'user_id']);

            // Add new unique constraints for user and session
            $table->unique(['product_id', 'user_id']);
            $table->unique(['product_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
