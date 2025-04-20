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
        Schema::create('wheels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('spins_per_user')->default(1); // Max spins per user
            $table->integer('spins_duration')->default(24); // Cooldown period in hours
            $table->timestamps();

            $table->enum('display_rules', [
                'all_pages',
                'specific_pages',
                'page_group',
                'all_except_specific',
                'all_except_group'
            ])->default('all_pages');

            $table->json('specific_pages')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wheels');
    }
};
