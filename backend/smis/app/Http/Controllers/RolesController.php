<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    // List all roles
    public function index()
    {
        return response()->json(Roles::all());
    }

    // Save a new Role
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:20|unique:tbl_roles,role_name',
        ]);

        $role = Roles::create($validated);
        return response()->json($role, 201);
    }

    // Show a specific Role
    public function show(Roles $role)
    {
        return response()->json($role);
    }

    // Update a Role
    public function update(Request $request, Roles $role)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:20|unique:tbl_roles,role_name,' . $role->id,
        ]);

        $role->update($validated);
        return response()->json($role);
    }

    // Delete a Role
    public function destroy(Roles $role)
    {
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
