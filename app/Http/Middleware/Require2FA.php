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
            // Allow the profile route itself so the user can enable 2FA
            if ($request->routeIs('*.profile*') || $request->routeIs('*.profile.2fa') || $request->routeIs('logout')) {
                return $next($request);
            }

            return redirect()->route($this->profileRoute($user->role))
                ->with('warning', '2FA is required for your account type. Please enable two-factor authentication to continue.');
        }

        return $next($request);
    }

    private function profileRoute(string $role): string
    {
        return match ($role) {
            'provider'                      => 'provider.dashboard',
            'facility_admin', 'super_admin' => 'admin.dashboard',
            'pharmacist'                    => 'pharmacy.dashboard',
            default                         => 'login',
        };
    }
}
