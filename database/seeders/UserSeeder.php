<?php

namespace database\seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin အကောင့်
        User::updateOrCreate(
            ['email' => 'admin@pos.com'],
            [
                'name' => 'Mg Kyaw (Admin)',
                'password' => Hash::make('password123'), // စကားဝှက်ကို Hash ဖြင့် လုံခြုံအောင်လုပ်ခြင်း
                'role' => 'admin',
            ]
        );

        // 2. Manager အကောင့်
        User::updateOrCreate(
            ['email' => 'manager@pos.com'],
            [
                'name' => 'Ma Su (Manager)',
                'password' => Hash::make('password123'),
                'role' => 'manager',
            ]
        );

        // 3. Cashier အကောင့်
        User::updateOrCreate(
            ['email' => 'cashier@pos.com'],
            [
                'name' => 'Mg Mg (Cashier)',
                'password' => Hash::make('password123'),
                'role' => 'cashier',
            ]
        );
    }
}
