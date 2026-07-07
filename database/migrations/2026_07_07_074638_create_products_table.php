<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Foreign Key to Categories Table
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            $table->string('name_en');
            $table->string('name_mm');
            $table->string('product_code')->unique();
            $table->text('description')->nullable();

            // ငွေကြေးဆိုင်ရာ ဒေတာများ (Total digits = 12, Decimal places = 2)
            $table->decimal('cost_price', 12, 2);
            $table->decimal('selling_price', 12, 2);

            $table->integer('stock_quantity')->default(0);
            $table->integer('alert_quantity')->default(10); // လက်ကျန်နည်းရင် သတိပေးဖို့
            $table->string('image')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
