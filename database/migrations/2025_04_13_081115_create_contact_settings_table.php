<?php

use App\Models\ContactSetting;
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
        Schema::create('contact_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $settings = [
            'phone1' => '0201 203 2032',
            'phone2' => '0201 203 2032',
            'mobile1' => '201-123-39223',
            'mobile2' => '02-123-3928',
            'email1' => 'porto@gmail.com',
            'email2' => 'porto@portotemplate.com',
            'skype1' => 'porto_skype',
            'skype2' => 'porto_templete',
        ];

        foreach ($settings as $key => $value) {
            ContactSetting::create([
                'key' => $key,
                'value' => $value
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_settings');
    }
};
