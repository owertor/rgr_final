<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@restaurant.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => 'admin', // Администратор получает роль 'admin'
                'email_verified_at' => now(),
            ]
        );
    }
}
