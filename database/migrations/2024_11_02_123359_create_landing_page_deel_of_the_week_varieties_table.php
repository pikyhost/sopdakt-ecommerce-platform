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
        Schema::create('landing_page_deal_of_the_week_varieties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('landing_page_deal_of_the_week_section_id');
            $table->foreign('landing_page_deal_of_the_week_section_id', 'landing_page_deal_of_the_week_section_id_fk')
                ->references('id')->on('landing_page_deal_of_the_week_sections')->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Size::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Color::class)->constrained()->cascadeOnDelete();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_deel_of_the_week_varieties');
    }
};
