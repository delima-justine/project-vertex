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
            'Property Custodian' => Office::firstOrCreate(['office_name' => 'Property Custodian']),
        ];

        User::firstOrCreate([
            'email' => 'propertyc.pup@gmail.com',
        ], [
            'first_name' => 'Property',
            'middle_initial' => '',
            'last_name' => 'Custodian',
            'password' => Hash::make('Property2026!'),
            'role_id' => $roles['SuperAdmin']->id,
            'office_id' => $offices['Property Custodian']->id,
        ]);

        $this->call([
            PermissionSeeder::class,
            SystemSettingSeeder::class,
        ]);
    }
}
