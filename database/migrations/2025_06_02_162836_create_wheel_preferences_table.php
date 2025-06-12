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
        Schema::create('wheel_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('wheel_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->timestamp('hide_until')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'wheel_id']);
            $table->unique(['session_id', 'wheel_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wheel_preferences');
    }
};
