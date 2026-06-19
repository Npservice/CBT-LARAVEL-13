<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckWebPermission
{
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        $userData = json_decode($request->cookie('user'), true);

        if (!$userData) {
            return redirect('/login');
        }

        $role        = $userData['role'] ?? null;
        $permissions = $userData['permissions'] ?? [];

        // Admin melewati semua permission check
        if ($role === 'admin') {
            return $next($request);
        }

        if (!in_array($permission, $permissions)) {
            return redirect('/admin');
        }

        return $next($request);
    }
}
