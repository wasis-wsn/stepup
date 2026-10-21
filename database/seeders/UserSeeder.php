<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin account
        User::firstOrCreate(
            ['email' => 'admin@stepup.test'],
            [
                'name' => 'STEPUP Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Regular user account
        User::firstOrCreate(
            ['email' => 'user@stepup.test'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );
    }
}
