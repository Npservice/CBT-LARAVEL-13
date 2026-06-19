<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Get authenticated user via Sanctum
        $user = $request->user('sanctum');

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Admins have all permissions
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check if user has the required permission
        if ($user->hasPermission($permission)) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }
}
