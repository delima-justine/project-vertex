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
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        // Find user in the tbl_user table 
        $user = User::where('email', $fields['email'])->first();

        // Check if user exists and password is correct
        // Hash::check compares the plain text password with the hashed password in DB
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Invalid credentials'
            ], 401); // 401 = Unauthorized
        }

        // Create new api token for this specific user
        // auth_token is just a label for the token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the user data and token in the response
        return response([
            'user' => $user,
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
