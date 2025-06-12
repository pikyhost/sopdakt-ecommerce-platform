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
        Schema::create('landing_page_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('landing_pages')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('address');
            $table->foreignIdFor(\App\Models\Governorate::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Country::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Color::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Size::class)->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_orders');
    }
};
