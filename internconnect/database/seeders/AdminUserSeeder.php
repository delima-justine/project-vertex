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
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@roc.ph',
            'contact_number' => '09123456789',
            'user_role' => 'HR', // Or 'HR' based on your enum
            'status' => 'Active',
            'password' => Hash::make('password123'),
        ]);
    }
}