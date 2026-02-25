<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@officeone.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'active' => true,
        ]);

        // Update existing test user to be a customer
        $testUser = User::where('email', 'test@example.com')->first();
        if ($testUser) {
            $testUser->update([
                'role' => 'Customer',
                'active' => true,
            ]);
        }

        // Create additional sample users
        User::create([
            'name' => 'John Customer',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'Customer',
            'active' => true,
        ]);

        User::create([
            'name' => 'Jane Customer',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => 'Customer',
            'active' => true,
        ]);
    }
}
