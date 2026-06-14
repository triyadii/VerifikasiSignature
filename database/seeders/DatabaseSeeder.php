<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Admin Account
        User::updateOrCreate(
            ['email' => 'admin@tte.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // Default User Account
        User::updateOrCreate(
            ['email' => 'user@tte.local'],
            [
                'name' => 'Standard User',
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]
        );
    }
}
