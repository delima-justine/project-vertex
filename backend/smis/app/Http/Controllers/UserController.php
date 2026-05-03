<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $limit = (int) $request->query('limit', 10);
        $limit = $limit > 0 ? min($limit, 100) : 10;

        $search = $request->query('search');

        $users = User::with(['role.permissions', 'permissions'])
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('role', function ($query) use ($search) {
                            $query->where('role_name', 'like', "%{$search}%");
                        });
                });
            })
            ->paginate($limit);

        return response()->json($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'middle_initial' => 'nullable|string|size:1',
            'last_name' => 'required|string|max:50',
            'email' => [
                'required',
                'email',
                Rule::unique('tbl_user')->whereNull('deleted_at')
            ],
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|integer|exists:tbl_roles,id',
            'office_name' => 'required|string|max:100',
            'permission_ids' => 'sometimes|array',
            'permission_ids.*' => 'integer|exists:tbl_permissions,id',
        ]);

        // Find or create office
        $office = \App\Models\Office::firstOrCreate(['office_name' => $validated['office_name']]);
        $validated['office_id'] = $office->id;

        // Generate a random password if none is provided
        $password = $validated['password'] ?? Str::random(12);
        $validated['password'] = Hash::make($password);

        // Check if a soft-deleted user with this email already exists
        $user = User::withTrashed()->where('email', $validated['email'])->first();

        if ($user) {
            // Restore and update the existing user
            $user->restore();
            $user->update($validated);
        } else {
            // Create a new user
            $user = User::create($validated);
        }

        // Sync permissions
        if (isset($validated['permission_ids'])) {
            $user->permissions()->sync($validated['permission_ids']);
        }

        // Send reset password email so the user can set their own password
        Password::broker()->sendResetLink(['email' => $user->email]);

        return response()->json($user->load(['role.permissions', 'permissions']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json($user->load(['role.permissions', 'permissions']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:50',
            'middle_initial' => 'sometimes|nullable|string|size:1',
            'last_name' => 'sometimes|required|string|max:50',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('tbl_user', 'email')->ignore($user->id),
            ],
            'password' => 'sometimes|required|string|min:8',
            'role_id' => 'sometimes|required|integer|exists:tbl_roles,id',
            'office_name' => 'sometimes|required|string|max:100',
            'permission_ids' => 'sometimes|array',
            'permission_ids.*' => 'integer|exists:tbl_permissions,id',
        ]);

        if (isset($validated['office_name'])) {
            $office = \App\Models\Office::firstOrCreate(['office_name' => $validated['office_name']]);
            $validated['office_id'] = $office->id;
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        // Sync permissions
        if (isset($validated['permission_ids'])) {
            $user->permissions()->sync($validated['permission_ids']);
        }

        return response()->json($user->load(['role.permissions', 'permissions']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
