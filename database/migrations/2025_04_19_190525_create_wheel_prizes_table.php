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
        Schema::create('wheel_prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wheel_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Displayed name, e.g., "10% Off"
            $table->enum('type', ['discount', 'coupon', 'points', 'product', 'none'])->default('none');
            $table->integer('value')->nullable(); // e.g., number of points
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('discount_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('probability')->default(10); // Weight of winning this prize
            $table->boolean('is_available')->default(true);
            $table->integer('daily_limit')->nullable();
            $table->integer('total_limit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wheel_prizes');
    }
};
