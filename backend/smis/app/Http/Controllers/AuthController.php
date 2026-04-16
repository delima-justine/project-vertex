<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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

        // Return the user data and token in the response
        return response([
            'user' => $user->load('role', 'office'),
            'token' => $token
        ], 200); // 200 = Success
    }

    // Logout
    public function logout(Request $request)
    {
        // Revoke (delete) the current token that was used for this request
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Logged out successfully'
        ], 200); // 200 = Success
    }
}
