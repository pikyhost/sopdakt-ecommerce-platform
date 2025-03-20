<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('tax_percentage')->default(0); // todo: I added this now
            $table->string('site_name')->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('cascade');
            $table->string('logo_en')->nullable();
            $table->string('logo_ar')->nullable();
            $table->string('dark_logo_en')->nullable();
            $table->string('dark_logo_ar')->nullable();
            $table->string('favicon')->nullable();
            $table->timestamps();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('facebook')->nullable();
            $table->string('youtube')->nullable();
            $table->string('instagram')->nullable();
            $table->string('x')->nullable(); // Twitter is now X
            $table->string('snapchat')->nullable();
            $table->string('tiktok')->nullable();
            $table->boolean('shipping_type_enabled')->default(true);

            // Indexing for faster lookup
            $table->index('currency_id');
        });

        // Insert default settings record
        DB::table('settings')->insert([
            'logo_en'     => 'assets/images/clients/client1.png',
            'logo_ar'     => 'assets/images/clients/client1.png',
            'favicon'  => 'assets/images/clients/client1.png',
            'site_name_en' => 'My E-Commerce',
            'site_name_ar' => 'متجري الإلكتروني',
            'currency_id' => null, // You can update this with a default currency ID if needed
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
