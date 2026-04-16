<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response(['message' => 'Unauthenticated'], 401);
        }

        // Ensure role relationship is loaded
        if (!$user->role) {
            return response(['message' => 'User has no assigned role'], 403);
        }

        $currentRole = strtolower($user->role->role_name);
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($currentRole, $allowedRoles)) {
            return response(['message' => 'Forbidden: You do not have the required role. Required: ' . implode(', ', $roles) . '. Your role: ' . $user->role->role_name], 403);
        }

        return $next($request);
    }
}
