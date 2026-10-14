<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        if (!$user->is_active) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account has been deactivated. Please contact support.'], 401);
            }
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['login' => 'Your account has been deactivated. Please contact support.']);
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
