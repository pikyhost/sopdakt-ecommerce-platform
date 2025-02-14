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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(\App\Models\Category::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->unique(); // Unique identifier for the product
            $table->integer('price');
            $table->text('description');
            $table->string('slug')->unique();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->integer('after_discount_price')->nullable();
            $table->dateTime('discount_start')->nullable();
            $table->dateTime('discount_end')->nullable();
            $table->integer('views')->default(0);
            $table->integer('sales')->default(0);
            $table->integer('fake_average_rating')->default(0);

            $table->foreignId('label_id')->nullable()->constrained('labels')->nullOnDelete();
            $table->tinyText('summary')->nullable();
            $table->integer('quantity')->default(0); // Total stock available
            $table->json('custom_attributes')->nullable(); // JSON for custom key-value attributes

            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
