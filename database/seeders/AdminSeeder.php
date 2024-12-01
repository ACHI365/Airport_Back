<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Adjust if your User model is in a different namespace
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // Check if the admin already exists by email
            [
            'id' => 1000,
            'email' => 'admin@example.com',
            'first_name' => 'admin',
            'last_name' => 'admin',
            'password' => Hash::make('admin_password'), // Use a secure password
            'email_verified_at' => now(),
            'is_admin' => true, // Ensure your user model has an `is_admin` attribute or equivalent
            'created_at' => now(),
            'updated_at' => now(),
            'verification_token' => null, // Set to null or generate a token if needed
            ]
        );
    }
}


