<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Rules\SafeFileType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Shared account endpoints for every authenticated role (users and admins):
 * profile ("me"), avatar upload, 2FA setup and OTP email verification.
 */
class AccountController extends Controller
{
    // == Me ====================================================================

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = [
            'user'           => $user,
            'role'           => $user->role,
            'email_verified' => $user->hasVerifiedEmail(),
        ];

        if ($user instanceof \App\Models\User && $user->role === 'patient') {
            $data['patient'] = $user->patient;
        }

        return response()->json($data);
    }

    // == Avatar ================================================================

    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048',
                         'dimensions:max_width=3000,max_height=3000',
                         new SafeFileType(['image/jpeg', 'image/png', 'image/webp'])],
        ]);

        /** @var \App\Models\User|\App\Models\Admin $user */
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $ext = match($request->file('avatar')->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg',
        };
        $name = 'avatars/' . $user->id . '_' . Str::uuid() . '.' . $ext;
        $path = $request->file('avatar')->storeAs('', $name, 'public');

        $user->update(['avatar' => $path]);

        return response()->json(['message' => 'Profile picture updated successfully.', 'avatar_url' => asset('storage/' . $path)]);
    }

    // == 2FA ===================================================================

    public function show2fa(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
            'has_phone'          => (bool) $user->phone,
        ]);
    }

    public function toggle2fa(Request $request): JsonResponse
    {
        $user    = $request->user();
        $enabled = !$user->two_factor_enabled;

        if ($enabled && !$user->phone) {
            return response()->json(['message' => 'A verified phone number is required to enable two-factor authentication.'], 422);
        }

        if (!$enabled) {
            $request->validate(['current_password' => 'required']);
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'Current password is incorrect.', 'errors' => ['current_password' => ['Current password is incorrect.']]], 422);
            }
        }

        $user->two_factor_enabled = $enabled;
        $user->save();

        return response()->json([
            'message'            => $enabled
                ? 'Two-factor authentication enabled. You will receive an SMS code on each login.'
                : 'Two-factor authentication disabled.',
            'two_factor_enabled' => $enabled,
        ]);
    }

    // == Email verification (OTP) ==============================================

    public function emailStatus(Request $request): JsonResponse
    {
        $user = $request->user();
        $verified = $user->hasVerifiedEmail();

        // Proactively generate a code when none is on file (pre-OTP accounts).
        if (!$verified && empty($user->email_verification_code)) {
            try {
                $user->sendEmailVerificationOtpNotification();
            } catch (\Throwable $e) {
                Log::warning('Proactive email-verification OTP send failed for user ' . $user->id . ': ' . $e->getMessage());
            }
        }

        return response()->json([
            'verified'     => $verified,
            'code_on_file' => !$verified && !empty($user->email_verification_code),
            'email'        => $user->email,
        ]);
    }

    public function emailVerify(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|digits:6']);

        $user = $request->user();
        $user = $user::find($user->id); // reload - avoid a stale cached model

        $result = $user->consumeEmailVerificationOtp($request->code);

        return match ($result) {
            'verified' => response()->json(['message' => 'Email verified successfully. Welcome to MedRecords.', 'verified' => true]),
            'already'  => response()->json(['message' => 'Your email is already verified.', 'verified' => true]),
            'expired'  => response()->json(['message' => 'This code has expired. Please request a new one.', 'code' => $result], 422),
            'no_code'  => response()->json(['message' => 'No verification code on file. Please request a resend.', 'code' => $result], 422),
            'too_many' => response()->json(['message' => 'Too many incorrect attempts. Please request a new code.', 'code' => $result], 422),
            'invalid'  => response()->json(['message' => 'Incorrect code. Please try again.', 'code' => $result], 422),
            default    => response()->json(['message' => 'Verification failed. Please try again.'], 422),
        };
    }

    public function emailResend(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Your email is already verified.', 'verified' => true]);
        }

        try {
            $user->sendEmailVerificationOtpNotification();
        } catch (\Throwable $e) {
            Log::warning('Resend email-verification OTP failed for user ' . $user->id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Could not send a new code right now. Please try again shortly.'], 503);
        }

        return response()->json(['message' => 'A new 6-digit verification code has been sent to your email.']);
    }
}
