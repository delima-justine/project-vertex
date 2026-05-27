<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
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

        $users = User::with(['role.permissions', 'permissions', 'office'])
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
            $oldValues = $user->toArray();
            // Restore and update the existing user
            $user->restore();
            
            if ($request->has('permission_ids')) {
                if (is_array($request->permission_ids)) {
                    $validated['has_custom_permissions'] = true;
                    $user->permissions()->sync($request->permission_ids);
                } else {
                    $validated['has_custom_permissions'] = false;
                    $user->permissions()->detach();
                }
            }

            $user->update($validated);
            AuditService::log('RESTORE', $user, "Restored and updated user: {$user->email}", $oldValues, $user->fresh()->toArray());
        } else {
            // Create a new user
            if ($request->has('permission_ids') && is_array($request->permission_ids)) {
                $validated['has_custom_permissions'] = true;
            }
            $user = User::create($validated);

            if ($request->has('permission_ids')) {
                if (is_array($request->permission_ids)) {
                    $user->permissions()->sync($request->permission_ids);
                }
            }
            AuditService::log('CREATE', $user, "Created new user: {$user->email}", null, $user->toArray());
        }

        // Send reset password email so the user can set their own password
        Password::broker()->sendResetLink(['email' => $user->email]);

        return response()->json($user->load(['role.permissions', 'permissions', 'office']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json($user->load(['role.permissions', 'permissions', 'office']));
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
            'permission_ids' => 'sometimes|nullable|array',
            'permission_ids.*' => 'integer|exists:tbl_permissions,id',
        ]);

        if (isset($validated['office_name'])) {
            $office = \App\Models\Office::firstOrCreate(['office_name' => $validated['office_name']]);
            $validated['office_id'] = $office->id;
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $oldValues = $user->toArray();
        
        // Handle custom permissions flag
        if ($request->has('permission_ids')) {
            if (is_array($request->permission_ids)) {
                $validated['has_custom_permissions'] = true;
                $user->permissions()->sync($request->permission_ids);
            } else {
                $validated['has_custom_permissions'] = false;
                $user->permissions()->detach();
            }
        }

        $user->update($validated);
        
        $newValues = $user->fresh(['role', 'permissions', 'office'])->toArray();
        AuditService::log('UPDATE', $user, "Updated user: {$user->email}", $oldValues, $newValues);

        return response()->json($user->load(['role.permissions', 'permissions', 'office']));
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

        $oldValues = $user->toArray();
        $user->delete();

        AuditService::log('DELETE', $user, "Deleted user: {$user->email}", $oldValues);

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * List all administrative users (Admin and SuperAdmin).
     */
    public function listAdmins()
    {
        $admins = User::whereHas('role', function ($query) {
            $query->whereIn('role_name', ['Admin', 'SuperAdmin']);
        })->get();

        return response()->json($admins);
    }
}
