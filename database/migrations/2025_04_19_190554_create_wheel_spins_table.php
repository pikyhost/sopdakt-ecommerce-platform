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
        Schema::create('wheel_spins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Contact::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->uuid('session_id')->nullable();
            $table->foreignId('wheel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wheel_prize_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_winner')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wheel_spins');
    }
};
