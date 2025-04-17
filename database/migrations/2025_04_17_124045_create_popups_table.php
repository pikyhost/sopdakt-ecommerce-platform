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
        Schema::create('popups', function (Blueprint $table) {
            $table->id();

            $table->string('title');                            // Popup title
            $table->text('description');                        // Main popup content

            $table->string('image_path')->nullable();           // Optional image path
            $table->string('cta_text');                         // Call-to-action button text
            $table->string('cta_link');                         // CTA redirect URL

            $table->unsignedInteger('delay_seconds')->default(5);  // Delay in seconds before showing popup

            $table->boolean('is_active')->default(true);        // Popup enabled or not

            $table->enum('display_rules', [
                'all_pages',
                'specific_pages',
                'page_group'
            ])->default('all_pages');                           // Where popup shows

            $table->json('specific_pages')->nullable();         // Used if display_rules = specific_pages

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popups');
    }
};
