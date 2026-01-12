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
        User::create([
            'first_name' => 'Ron Oliver',
            'last_name' => 'Clarin',
            'email' => 'admin@roc.ph',
            'contact_number' => '09123456789',
            'user_role' => 'HR',
            'status' => 'Active',
            'password' => Hash::make('password123'),
        ]);

        // Sample Coordinator
        User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'coordinator@roc.ph',
            'contact_number' => '09123456789',
            'user_role' => 'Coordinator',
            'status' => 'Active',
            'password' => Hash::make('password123'),    
        ]);
    }
}