<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $userCookie = $request->cookie('user');

        if (!$userCookie) {
            return redirect('/login');
        }

        $userData = json_decode($userCookie, true);
        $role = $userData['role'] ?? null;

        if (!$role || !in_array($role, $roles)) {
            // Redirect to their own area
            return redirect($role === 'siswa' ? '/siswa' : '/admin');
        }

        return $next($request);
    }
}
