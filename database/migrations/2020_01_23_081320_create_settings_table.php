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
            $table->string('site_name_en')->nullable();
            $table->string('site_name_ar')->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('cascade');
            $table->string('logo_en')->nullable();
            $table->string('logo_ar')->nullable();
            $table->string('dark_logo_en')->nullable();
            $table->string('dark_logo_ar')->nullable();
            $table->string('favicon_en')->nullable();
            $table->string('favicon_ar')->nullable();
            $table->timestamps();

            // Indexing for faster lookup
            $table->index('currency_id');
        });

        // Insert default settings record
        DB::table('settings')->insert([
            'logo_en'     => 'assets/images/clients/client1.png',
            'logo_ar'     => 'assets/images/clients/client1.png',
            'favicon_en'  => 'assets/images/clients/client1.png',
            'favicon_ar'  => 'assets/images/clients/client1.png',
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
