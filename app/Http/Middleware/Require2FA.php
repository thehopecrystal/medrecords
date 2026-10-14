<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Require2FA
{
    private const ENFORCED_ROLES = ['provider', 'facility_admin', 'super_admin', 'pharmacist'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, self::ENFORCED_ROLES)) {
            return $next($request);
        }

        if (!$user->two_factor_enabled) {
            // Allow the shared 2FA setup page and logout — nothing else.
            if ($request->routeIs('account.2fa', 'account.2fa.toggle', 'logout', 'api.account.2fa', 'api.account.2fa.toggle', 'api.auth.logout')) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Two-factor authentication is required for your account. Please enable it to continue.',
                    'code'    => 'two_factor_required',
                ], 403);
            }

            return redirect()->route('account.2fa')
                ->with('warning', 'Two-factor authentication is required for your account. Please enable it to continue.');
        }

        return $next($request);
    }
}
