<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seeder တွေ ပြန်ခေါ်
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            UserSeeder::class,
        ]);
    }
}
