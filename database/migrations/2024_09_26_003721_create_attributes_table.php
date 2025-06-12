<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Translatable attribute name
            $table->string('description')->nullable(); // Translatable attribute description (Stored as string)
            $table->enum('type', ['boolean', 'select', 'text']); // Attribute type
            $table->json('values')->nullable(); // Store possible values for select type
            $table->json('default_value')->nullable(); // Store default value dynamically
            $table->timestamps();
        });

        Schema::create('attribute_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->string('value')->nullable(); // Store attribute value
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_product');
        Schema::dropIfExists('attributes');
    }
};
