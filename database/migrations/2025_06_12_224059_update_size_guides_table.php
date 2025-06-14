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
            $table->integer('min_height')->nullable()->after('size_id');
            $table->integer('max_height')->nullable()->after('min_height');
            $table->integer('min_weight')->nullable()->after('max_height');
            $table->integer('max_weight')->nullable()->after('min_weight');
            $table->integer('min_age')->nullable()->after('max_weight');
            $table->integer('max_age')->nullable()->after('min_age');
            $table->integer('min_shoulder_width')->nullable()->after('max_age');
            $table->integer('max_shoulder_width')->nullable()->after('min_shoulder_width');
            $table->integer('bust_measurement')->nullable()->after('max_shoulder_width');
            $table->integer('length_measurement')->nullable()->after('bust_measurement');
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
