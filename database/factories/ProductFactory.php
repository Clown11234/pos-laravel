<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $cost = $this->faker->numberBetween(1000, 50000);
        $selling = $cost + $this->faker->numberBetween(500, 10000);

        return [
            // Cate ရှိရင်ယူမယ် ၊ မရှိရင် အသစ်ဆောက်
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'name_en' => $this->faker->words(3, true),
            'name_mm' => 'ကုန်ပစ္စည်း အမှတ်-' . $this->faker->unique()->numberBetween(100, 999),
            'product_code' => 'PROD-' . $this->faker->unique()->numberBetween(10000, 99999),
            'description' => $this->faker->sentence(),
            'cost_price' => $cost,
            'selling_price' => $selling,
            'stock_quantity' => $this->faker->numberBetween(10, 100),
            'alert_quantity' => 5,
            'image' => null,
        ];
    }
}
