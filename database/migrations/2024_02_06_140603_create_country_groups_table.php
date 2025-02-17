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
        Schema::create('country_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Translatable group name
            $table->integer('cost')->default(0);
            $table->string('shipping_estimate_time')->default('0-0');
            $table->timestamps();
        });

        Schema::create('country_group_country', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_groups');
    }
};
