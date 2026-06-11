<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireMdcnVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== 'provider') {
            return $next($request);
        }

        $provider = $user->provider;

        if (!$provider || !$provider->mdcn_verified) {
            return redirect()->route('provider.pending-verification');
        }

        return $next($request);
    }
}
