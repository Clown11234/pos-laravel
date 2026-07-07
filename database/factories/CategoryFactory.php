<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        // Fake Data
        $categories = [
            ['en' => 'Electronics', 'mm' => 'အီလက်ထရောနစ်'],
            ['en' => 'Beverages', 'mm' => 'ဖျော်ရည်နှင့် အဖျော်ယမကာ'],
            ['en' => 'Snacks', 'mm' => 'မုန့်ပဲသရေစာ'],
            ['en' => 'Cosmetics', 'mm' => 'အလှကုန်ပစ္စည်းများ'],
            ['en' => 'Canned Foods', 'mm' => 'စည်သွပ်ဘူး']
        ];

        $choice = $this->faker->randomElement($categories);

        return [
            'name_en' => $choice['en'],
            'name_mm' => $choice['mm'],
            'slug' => Str::slug($choice['en']) . '-' . rand(100, 999),
            'is_active' => true,
        ];
    }
}
