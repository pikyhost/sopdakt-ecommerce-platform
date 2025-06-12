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
        // Create wheels table
        Schema::create('wheels', function (Blueprint $table) {
            $table->id();
            $table->integer('daily_spin_limit')->default(2);
            $table->integer('time_between_spins_minutes')->default(30);
            $table->boolean('require_phone')->default(true);
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->enum('display_rules', [
                'all_pages',
                'specific_pages',
                'page_group',
                'all_except_specific',
                'all_except_group'
            ])->default('all_pages');

            $table->unsignedInteger('popup_order')->default(0);
            $table->unsignedInteger('show_interval_minutes')->default(60);
            $table->unsignedInteger('delay_seconds')->default(60);
            $table->unsignedInteger('duration_seconds')->default(60);
            $table->unsignedInteger('dont_show_again_days')->default(7);

            $table->json('specific_pages')->nullable();
            $table->timestamps();
        });

// Create wheel_prizes table
        Schema::create('wheel_prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wheel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->integer('probability')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

// Create wheel_spins table
        Schema::create('wheel_spins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wheel_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('prize_id')->constrained('wheel_prizes');
            $table->string('ip_address');
            $table->timestamp('next_spin_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'wheel_id']);
            $table->index(['session_id', 'wheel_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spins');
    }
};
