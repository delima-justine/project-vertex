<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Roles;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        return response()->json(Permission::all());
    }

    public function getByRole($roleId)
    {
        $role = Roles::with('permissions')->find($roleId);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
        return response()->json($role->permissions);
    }
}
