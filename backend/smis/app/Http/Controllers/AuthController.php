<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Forgot Password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // We use the standard Password broker to send the reset link
        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response(['message' => __($status)], 200)
            : response(['message' => __($status)], 400);
    }

    // Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response(['message' => __($status)], 200)
            : response(['message' => __($status)], 400);
    }

    // Login Function
    public function login(Request $request)
    {
        // Validate the request
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $loginValue = $fields['email'];

        // Find all users matching email or office name
        $users = User::where('email', $loginValue)
            ->orWhereHas('office', function($query) use ($loginValue) {
                $query->where('office_name', $loginValue);
            })
            ->get();

        $user = null;
        foreach ($users as $u) {
            if (Hash::check($fields['password'], $u->password)) {
                $user = $u;
                break;
            }
        }

        // Check if user exists and password is correct
        // Hash::check compares the plain text password with the hashed password in DB
        if (!$user) {
            return response([
                'message' => 'Invalid credentials'
            ], 401); // 401 = Unauthorized
        }

        // Create new api token for this specific user
        // auth_token is just a label for the token
        $token = $user->createToken('auth_token')->plainTextToken;

        AuditService::log('LOGIN', $user, "User logged in: {$user->email}", null, null, $user);

        $allPermissions = $user->permissions->isNotEmpty() 
            ? $user->permissions->pluck('name') 
            : $user->role->permissions->pluck('name');

        // Return the user data and token in the response
        return response([
            'user' => $user->load('role', 'office'),
            'permissions' => $allPermissions,
            'token' => $token
        ], 200); // 200 = Success
    }

    // Logout
    public function logout(Request $request)
    {
        $user = $request->user();
        AuditService::log('LOGOUT', $user, "User logged out: {$user->email}");

        // Revoke (delete) the current token that was used for this request
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Logged out successfully'
        ], 200); // 200 = Success
    }

    // Change Password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response([
                'message' => 'Current password does not match.'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        AuditService::log('CHANGE_PASSWORD', $user, "User changed their password: {$user->email}");

        return response([
            'message' => 'Password updated successfully.'
        ], 200);
    }
}
