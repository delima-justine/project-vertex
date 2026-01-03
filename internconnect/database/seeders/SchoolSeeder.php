<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        School::create([
            'school_name' => 'University of the Philippines',
            'branch_campus' => 'Diliman',
            'address' => 'Quezon City, Philippines',
        ]);

        School::create([
            'school_name' => 'Ateneo de Manila University',
            'branch_campus' => 'Main Campus',
            'address' => 'Quezon City, Philippines',
        ]);

        School::create([
            'school_name' => 'De La Salle University',
            'branch_campus' => 'Manila',
            'address' => 'Manila, Philippines',
        ]);
    }
}
