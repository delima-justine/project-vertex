<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Check Reset Token
    public function checkResetToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if ($record && Hash::check($request->token, $record->token)) {
            $createdAt = Carbon::parse($record->created_at);
            $expiresAt = $createdAt->copy()->addMinutes(config('auth.passwords.users.expire'));

            if (Carbon::now()->greaterThan($expiresAt)) {
                return response(['message' => 'Token has expired.'], 400);
            }

            return response([
                'message' => 'Token is valid.',
                'expires_at' => $expiresAt->toIso8601String(),
                'server_time' => Carbon::now()->toIso8601String(),
            ], 200);
        }

        return response(['message' => 'Invalid token.'], 400);
    }

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

    // Resend Password Link (from email)
    public function resendPasswordLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        Password::broker()->sendResetLink(
            $request->only('email')
        );

        return redirect(config('app.frontend_url', 'http://localhost:4200') . '/forgot-password?status=resent');
    }

    // Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required', 
                'string', 
                'confirmed',
                PasswordRule::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
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
        $lockoutKey = 'login_lockout:' . $loginValue;
        $failedAttemptsKey = 'failed_attempts:' . $loginValue;

        // Check if account is currently locked
        if (Cache::has($lockoutKey)) {
            $lockoutUntil = Cache::get($lockoutKey);
            $secondsRemaining = Carbon::now()->diffInSeconds($lockoutUntil, false);
            
            if ($secondsRemaining > 0) {
                return response([
                    'message' => 'Too many failed attempts. Please try again in ' . ceil($secondsRemaining / 60) . ' minute(s).',
                    'seconds_remaining' => $secondsRemaining
                ], 429);
            }
        }

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
        if (!$user) {
            $failedCount = Cache::get($failedAttemptsKey, 0) + 1;
            Cache::put($failedAttemptsKey, $failedCount, now()->addDay());

            if ($failedCount % 3 === 0) {
                $lockoutMinutes = $failedCount / 3;
                Cache::put($lockoutKey, Carbon::now()->addMinutes($lockoutMinutes), Carbon::now()->addMinutes($lockoutMinutes));
                
                return response([
                    'message' => "Too many failed attempts. Account locked for $lockoutMinutes minute(s).",
                    'seconds_remaining' => $lockoutMinutes * 60
                ], 429);
            }

            return response([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Successful login: Reset failed attempts and lockout
        Cache::forget($failedAttemptsKey);
        Cache::forget($lockoutKey);

        // Create new api token for this specific user
        $token = $user->createToken('auth_token')->plainTextToken;

        AuditService::log('LOGIN', $user, "User logged in: {$user->email}", null, null, $user);

        $allPermissions = $user->has_custom_permissions
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
            'new_password' => [
                'required', 
                'string', 
                'confirmed',
                PasswordRule::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
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
