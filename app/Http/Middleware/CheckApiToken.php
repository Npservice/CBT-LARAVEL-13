<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApiToken
{
    public function handle(Request $request, Closure $next)
    {
        // Allow login page
        if ($request->is('login')) {
            return $next($request);
        }

        // Check token from cookie
        $token = $request->cookie('api_token');

        if (!$token) {
            $authHeader = $request->header('Authorization');
            if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
            }
        }

        if (!$token) {
            return redirect('/login')
                ->withCookie(cookie()->forget('api_token'))
                ->withCookie(cookie()->forget('user'));
        }

        // Token exists, proceed (validation done via API routes middleware)
        return $next($request);
    }
}
