<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'director_name', 'value' => 'DR. MARISSA B. FERRER'],
            ['key' => 'director_title', 'value' => 'DIRECTOR'],
            ['key' => 'custodian_name', 'value' => 'GINA A. DELA CRUZ'],
            ['key' => 'custodian_title', 'value' => 'Property Custodian'],
        ];

        foreach ($settings as $setting) {
            \App\Models\SystemSetting::updateOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }
}
