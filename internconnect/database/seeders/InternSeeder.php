<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InternSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Intern seeding logic here
        // Sample Intern
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'intern@roc.ph',
            'contact_number' => '09123456789',
            'user_role' => 'Intern',
            'status' => 'Active',
            'password' => Hash::make('password123'),    
            'coordinator_id' => 1,
        ]);
    }
}
