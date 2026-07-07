<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('name_en'); // အင်္ဂလိပ်လို အမျိုးအစား အမည်
            $table->string('name_mm'); // မြန်မာလို အမျိုးအစား အမည်
            $table->string('slug')->unique(); // URL သန့်ရှင်းစေရန် (ဥပမာ - electronic-appliances)
            $table->boolean('is_active')->default(true); // အလုပ်လုပ်မလုပ် သတ်မှတ်ချက်
            $table->softDeletes(); // ယာယီဖျက်သိမ်းမှုအတွက် deleted_at column တိုးပေးခြင်း
            $table->timestamps(); // created_at နှင့် updated_at တိုးပေးခြင်း
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
