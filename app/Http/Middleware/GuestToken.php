<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GuestToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('api_token');

        if ($token) {
            $userCookie = $request->cookie('user');
            $role = null;
            if ($userCookie) {
                $userData = json_decode($userCookie, true);
                $role = $userData['role'] ?? null;
            }
            return redirect($role === 'siswa' ? '/siswa' : '/admin');
        }

        return $next($request);
    }
}
