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
        Schema::create('size_guides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('size_id')->nullable(); // optional FK
            $table->integer('min_height')->nullable();
            $table->integer('max_height')->nullable();
            $table->integer('min_weight')->nullable();
            $table->integer('max_weight')->nullable();
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->integer('min_shoulder_width')->nullable();
            $table->integer('max_shoulder_width')->nullable();
            $table->integer('bust_measurement')->nullable();
            $table->integer('length_measurement')->nullable();
            $table->timestamps();
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
