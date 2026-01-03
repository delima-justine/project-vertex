<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Coordinator;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterUserAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Handle new school creation
            if ($data['selected_school'] === 'other') {
                $school = School::create([
                    'school_name' => $data['new_school_name'],
                    'address' => $data['new_school_address'],
                    'branch_campus' => $data['new_school_campus'],
                ]);
                $data['selected_school'] = $school->school_id;
            }

            if ($data['user_role'] === 'Coordinator') {
                // Generate unique coordinator ID: COORD-2026-XXXX
                $randomDigits = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $coordinatorId = 'COORD-2026-' . $randomDigits;

                // Create Coordinator record
                $coordinator = Coordinator::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'school_id' => $data['selected_school'],
                    'unique_key' => $coordinatorId,
                ]);

                // Create User record
                $user = User::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'contact_number' => $data['contact_number'],
                    'user_role' => 'Coordinator',
                    'school_id' => $data['selected_school'],
                    'coordinator_id' => $coordinator->coordinator_id,
                    'status' => 'Active',
                    'password' => $data['password'],
                ]);

                return $user;
            } elseif ($data['user_role'] === 'Intern') {
                // Create User record for Intern
                $user = User::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'contact_number' => $data['contact_number'],
                    'user_role' => 'Intern',
                    'school_id' => $data['selected_school'],
                    'status' => 'Applicant',
                    'password' => $data['password'],
                ]);

                return $user;
            }

            throw new \Exception('Invalid role specified.');
        });
    }
}