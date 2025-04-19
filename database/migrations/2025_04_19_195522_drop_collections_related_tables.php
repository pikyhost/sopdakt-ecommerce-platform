<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Optional: keep this empty if you're only dropping in down()
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_discount');
        Schema::dropIfExists('collection_product');
        Schema::dropIfExists('collections');
    }
};
