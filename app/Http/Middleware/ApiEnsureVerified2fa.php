<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Enforces the two-factor "pending" state for JWT logins.
 *
 * When 2FA is enabled, Api\AuthController issues a short-lived token carrying
 * the `2fa_pending` claim instead of a full session token. This middleware
 * rejects any such token for every route except the 2FA verification, 2FA
 * setup, refresh, logout and "me" endpoints.
 */
class ApiEnsureVerified2fa
{
    /** Routes reachable while the token is still 2FA-pending. */
    private const ALLOWED_ROUTES = [
        'api.auth.2fa.verify',
        'api.auth.refresh',
        'api.auth.logout',
        'api.auth.me',
        'api.account.2fa',
        'api.account.2fa.toggle',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
        } catch (JWTException) {
            // No token (or unusable token) — let the auth middleware produce the 401.
            return $next($request);
        }

        if (filter_var($payload->get('2fa_pending'), FILTER_VALIDATE_BOOLEAN)) {
            // Pending tokens expire 10 minutes after issue, regardless of TTL.
            $issuedAt = (int) $payload->get('iat', 0);
            if ($issuedAt && (now()->getTimestamp() - $issuedAt) > 600) {
                return response()->json([
                    'message' => 'Two-factor session expired. Please log in again.',
                    'code'    => 'two_factor_pending_expired',
                ], 401);
            }

            if (!$request->routeIs(self::ALLOWED_ROUTES)) {
                return response()->json([
                    'message'       => 'Two-factor authentication is required before the API can be used. Submit your OTP via POST /api/auth/2fa/verify.',
                    'code'          => 'two_factor_pending',
                    'requires_2fa'  => true,
                ], 403);
            }
        }

        return $next($request);
    }
}
