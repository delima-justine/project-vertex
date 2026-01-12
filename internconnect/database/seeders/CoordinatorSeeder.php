<?php

namespace Database\Seeders;

use App\Models\Coordinator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoordinatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Coordinator seeding logic here
        Coordinator::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'coordinator@roc.ph',
            'school_id' => 1,
            'unique_key' => 'UNIQUEKEY123', 
        ]);
    }
}
