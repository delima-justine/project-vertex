<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            'Admin' => Roles::firstOrCreate(['role_name' => 'Admin']),
            'SuperAdmin' => Roles::firstOrCreate(['role_name' => 'SuperAdmin']),
            'User' => Roles::firstOrCreate(['role_name' => 'User']),
        ];

        $offices = [
            'Registrar' => Office::firstOrCreate(['office_name' => 'Office of the University Registrar']),
            'Student Affairs' => Office::firstOrCreate(['office_name' => 'Office of Student Affairs']),
            'Dean' => Office::firstOrCreate(['office_name' => 'Office of the Dean']),
        ];

        User::firstOrCreate([
            'email' => 'superadmin@example.com',
        ], [
            'first_name' => 'Super',
            'middle_initial' => 'A',
            'last_name' => 'Admin',
            'password' => Hash::make('SuperAdmin123!'),
            'role_id' => $roles['SuperAdmin']->id,
            'office_id' => $offices['Registrar']->id,
        ]);
    }
}
