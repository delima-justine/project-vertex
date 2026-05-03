<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Roles;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Requests
            ['name' => 'view_pending_requests', 'description' => 'View Pending Requests'],
            ['name' => 'view_approved_requests', 'description' => 'View Approved Requests'],
            ['name' => 'view_released_requests', 'description' => 'View Released Requests'],
            ['name' => 'view_disapproved_requests', 'description' => 'View Disapproved Requests'],
            ['name' => 'edit_ris', 'description' => 'Edit RIS'],
            ['name' => 'deny_request', 'description' => 'Deny Request'],

            // Users
            ['name' => 'add_user', 'description' => 'Add User'],
            ['name' => 'edit_user', 'description' => 'Edit User'],
            ['name' => 'delete_user', 'description' => 'Delete User'],

            // Supply
            ['name' => 'add_supply', 'description' => 'Add Supply'],
            ['name' => 'edit_supply', 'description' => 'Edit Supply'],
            ['name' => 'delete_supply', 'description' => 'Delete Supply'],

            // Categories
            ['name' => 'add_category', 'description' => 'Add Category'],
            ['name' => 'delete_category', 'description' => 'Delete Category'],

            // Units
            ['name' => 'add_unit', 'description' => 'Add Unit'],
            ['name' => 'delete_unit', 'description' => 'Delete Unit'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }

        // Assign permissions to roles
        $superAdmin = Roles::where('role_name', 'SuperAdmin')->first();
        $admin = Roles::where('role_name', 'Admin')->first();
        $user = Roles::where('role_name', 'User')->first();

        $allPermissions = Permission::all();

        if ($superAdmin) {
            $superAdmin->permissions()->sync($allPermissions->pluck('id'));
        }

        if ($admin) {
            $admin->permissions()->sync($allPermissions->pluck('id'));
        }

        if ($user) {
            $userPermissions = Permission::whereIn('name', [
                'view_pending_requests',
                'view_approved_requests',
                'view_released_requests',
                'view_disapproved_requests',
            ])->get();
            $user->permissions()->sync($userPermissions->pluck('id'));
        }
    }
}
