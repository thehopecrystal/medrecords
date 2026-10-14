<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Facility;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * JWT authentication for the REST API.
 *
 * Mirrors the session-based web auth flows:
 *   - credential lookup across users (patient/provider/pharmacist) and admins
 *   - 2FA-at-login via a short-lived "pending" token + POST /api/auth/2fa/verify
 *   - patient / provider / organisation registration
 *   - password reset via Laravel's token broker
 */
class AuthController extends Controller
{
    /** Minutes a 2FA-pending token stays valid. */
    private const PENDING_TTL = 5;

    // == Login =================================================================

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        if (filter_var($request->login, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } elseif (preg_match('/^\+?[0-9]{7,15}$/', preg_replace('/[\s\-()]/', '', $request->login))) {
            $field = 'phone';
        } else {
            return $this->invalidCredentials();
        }

        $isAdmin = Admin::where($field, $request->login)->exists();
        $guard   = $isAdmin ? 'admin-api' : 'api';
        $model   = $isAdmin ? Admin::class : User::class;

        /** @var User|Admin $user */
        $user = $model::where($field, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->invalidCredentials();
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'Your account has been deactivated. Please contact support.'], 401);
        }

        // Super admins bypass OTP - direct sign-in only.
        $isSuper = $isAdmin && $user->role === 'super_admin';

        if (!$isSuper && $user->two_factor_enabled) {
            $channels = $this->issueLoginOtp($user);

            // Short-lived "pending" token: carries the 2fa_pending claim, which
            // ApiEnsureVerified2fa enforces everywhere else.
            $factory = JWTAuth::factory();
            $fullTtl = (int) config('jwt.ttl', 60);
            $factory->setTTL(self::PENDING_TTL);
            $pendingToken = auth($guard)->claims(['2fa_pending' => true])->login($user);
            $factory->setTTL($fullTtl);

            return response()->json([
                'requires_2fa' => true,
                'token_type'   => 'pending',
                'access_token' => $pendingToken,
                'expires_in'   => self::PENDING_TTL * 60,
                'otp_channels' => $channels,
                'message'      => 'Two-factor authentication required. Submit the 6-digit code to POST /api/auth/2fa/verify using the pending token.',
            ]);
        }

        $user->update(['last_login_at' => now()]);
        $token = auth($guard)->claims([])->login($user);

        return $this->tokenResponse($token, $user);
    }

    // == 2FA verification (pending token) ======================================

    public function verify2fa(Request $request): JsonResponse
    {
        $request->validate(['otp' => 'required|string|digits:6']);

        try {
            $payload = JWTAuth::parseToken()->getPayload();
        } catch (JWTException) {
            return response()->json(['message' => 'Session expired. Please log in again.'], 401);
        }

        if (!filter_var($payload->get('2fa_pending'), FILTER_VALIDATE_BOOLEAN)) {
            return response()->json(['message' => 'This token is not awaiting two-factor verification.'], 422);
        }

        $isAdmin = $payload->get('guard') === 'admins';
        $user    = $isAdmin ? Admin::find($payload->get('sub')) : User::find($payload->get('sub'));

        if (!$user) {
            return response()->json(['message' => 'Session expired. Please log in again.'], 401);
        }

        $expired  = !$user->otp_expires_at || now()->isAfter($user->otp_expires_at);
        $mismatch = !Hash::check($request->otp, $user->otp_code ?? '');

        if ($expired || $mismatch) {
            $user->otp_code       = null;
            $user->otp_expires_at = null;
            $user->save();

            return response()->json([
                'message' => $expired ? 'Code has expired. Please log in again.' : 'Invalid code. Please log in again.',
                'code'    => 'otp_invalid',
            ], 422);
        }

        $user->otp_code       = null;
        $user->otp_expires_at = null;
        $user->save();
        $user->update(['last_login_at' => now()]);

        // claims([]) clears any lingering custom claims on the shared JWT builder
        // before encoding the full-access token.
        $token = auth($isAdmin ? 'admin-api' : 'api')->claims([])->login($user);

        return $this->tokenResponse($token, $user, 'Two-factor verification passed.');
    }

    // == Registration ==========================================================

    public function register(Request $request): JsonResponse
    {
        return match ($request->input('reg_type', 'patient')) {
            'provider'           => $this->registerProvider($request),
            'hospital', 'clinic' => $this->registerOrg($request, $request->input('reg_type')),
            'pharmacy'           => $this->registerOrg($request, 'pharmacy'),
            default              => $this->registerPatient($request),
        };
    }

    private function registerPatient(Request $request): JsonResponse
    {
        $request->validate([
            'terms'      => 'accepted',
            'name'       => 'required|string|max:120',
            'email'      => 'required|email|unique:users,email',
            'phone'      => ['required', 'string', 'regex:/^\+[1-9]\d{7,14}$/', 'unique:users,phone'],
            'password'   => 'required|string|min:8|confirmed',
            'dob'        => 'nullable|date|before:today',
            'gender'     => 'nullable|in:male,female,other',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'genotype'   => 'nullable|in:AA,AS,SS,AC,SC',
            'state'      => 'nullable|string|max:60',
            'nin'        => 'nullable|regex:/^\d{11}$/|unique:users,nin',
        ], [
            'phone.regex'  => 'Phone must include a country code (e.g. +2348012345678).',
            'nin.regex'    => 'NIN must be exactly 11 digits.',
            'nin.unique'   => 'This NIN is already registered.',
            'email.unique' => 'This email address is already registered.',
            'phone.unique' => 'This phone number is already registered.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'nin'      => $request->nin ?: null,
            'password' => Hash::make($request->password),
        ]);
        $user->role = 'patient';
        $user->save();

        $patient = new Patient([
            'med_id'     => 'MR-' . strtoupper(Str::random(6)) . '-' . strtoupper(Str::random(6)),
            'dob'        => $request->dob ?: null,
            'gender'     => $request->gender ?: null,
            'blood_type' => $request->blood_type ?: null,
            'genotype'   => $request->genotype ?: null,
            'state'      => $request->state ?: null,
        ]);
        $patient->user_id = $user->id;
        $patient->save();

        $user->update(['last_login_at' => now()]);
        try { $user->sendEmailVerificationOtpNotification(); } catch (\Throwable $e) { Log::warning("Verification email failed for user {$user->id}: " . $e->getMessage()); }

        $token = auth('api')->claims([])->login($user);

        return $this->tokenResponse($token, $user, 'Registration successful. A 6-digit verification code has been emailed to you.', 201);
    }

    private function registerProvider(Request $request): JsonResponse
    {
        $request->validate([
            'terms'      => 'accepted',
            'name'       => 'required|string|max:120',
            'email'      => 'required|email|unique:users,email',
            'phone'      => ['required', 'string', 'regex:/^\+[1-9]\d{7,14}$/', 'unique:users,phone'],
            'password'   => 'required|string|min:8|confirmed',
            'specialty'  => 'required|string|max:120',
            'license_no' => 'nullable|string|max:50',
            'mdcn_folio' => 'nullable|string|max:50',
        ], [
            'phone.regex'        => 'Phone must include a country code (e.g. +2348012345678).',
            'email.unique'       => 'This email address is already registered.',
            'phone.unique'       => 'This phone number is already registered.',
            'specialty.required' => 'Please enter your specialty.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);
        $user->role = 'provider';
        $user->save();

        Provider::create([
            'user_id'     => $user->id,
            'facility_id' => null,
            'specialty'   => $request->specialty,
            'license_no'  => $request->license_no ?: null,
            'mdcn_folio'  => $request->mdcn_folio ?: null,
        ]);

        $user->update(['last_login_at' => now()]);
        try { $user->sendEmailVerificationOtpNotification(); } catch (\Throwable $e) { Log::warning("Verification email failed for user {$user->id}: " . $e->getMessage()); }

        $token = auth('api')->claims([])->login($user);

        return $this->tokenResponse($token, $user, 'Registration successful. A 6-digit verification code has been emailed to you.', 201);
    }

    private function registerOrg(Request $request, string $type): JsonResponse
    {
        $facilityTypes = ['hospital' => 'hospital', 'clinic' => 'clinic', 'pharmacy' => 'pharmacy'];
        $facilityType  = $facilityTypes[$type] ?? 'hospital';

        $request->validate([
            'terms'       => 'accepted',
            'name'        => 'required|string|max:120',
            'email'       => 'required|email|unique:admins,email',
            'phone'       => ['required', 'string', 'regex:/^\+[1-9]\d{7,14}$/', 'unique:admins,phone'],
            'password'    => 'required|string|min:8|confirmed',
            'org_name'    => 'required|string|max:200',
            'org_type'    => 'nullable|in:hospital,clinic,lab,dental,optical,pharmacy',
            'org_address' => 'nullable|string|max:255',
            'org_city'    => 'nullable|string|max:60',
            'org_state'   => 'nullable|string|max:60',
            'org_phone'   => 'nullable|string|max:20',
            'org_email'   => 'nullable|email|max:120',
            'org_license' => 'nullable|string|max:80',
        ], [
            'phone.regex'       => 'Phone must include a country code (e.g. +2348012345678).',
            'email.unique'      => 'This email address is already registered.',
            'phone.unique'      => 'This phone number is already registered.',
            'org_name.required' => 'Please enter your organisation name.',
        ]);

        $admin = Admin::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);
        $admin->role = 'facility_admin';
        $admin->save();

        Facility::create([
            'owner_user_id' => $admin->id,
            'name'          => $request->org_name,
            'type'          => $request->org_type ?: $facilityType,
            'license_no'    => $request->org_license ?: null,
            'address'       => $request->org_address ?: null,
            'city'          => $request->org_city ?: null,
            'state'         => $request->org_state ?: null,
            'phone'         => $request->org_phone ?: null,
            'email'         => $request->org_email ?: null,
        ]);

        $admin->update(['last_login_at' => now()]);
        try { $admin->sendEmailVerificationOtpNotification(); } catch (\Throwable $e) { Log::warning("Verification email failed for admin {$admin->id}: " . $e->getMessage()); }

        $token = auth('admin-api')->claims([])->login($admin);

        return $this->tokenResponse($token, $admin, 'Registration successful. A 6-digit verification code has been emailed to you.', 201);
    }

    // == Token lifecycle =======================================================

    public function refresh(): JsonResponse
    {
        try {
            $token = JWTAuth::parseToken()->refresh();
        } catch (JWTException) {
            return response()->json(['message' => 'Token cannot be refreshed. Please log in again.'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => (int) config('jwt.ttl', 60) * 60,
        ]);
    }

    public function logout(): JsonResponse
    {
        try {
            JWTAuth::parseToken()->invalidate();
        } catch (JWTException) {
            // Token already expired/invalid - treat as logged out.
        }

        return response()->json(['message' => 'Successfully logged out.']);
    }

    // == Password reset ========================================================

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $broker = Admin::where('email', $request->email)->exists()
            ? \Illuminate\Support\Facades\Password::broker('admins')
            : \Illuminate\Support\Facades\Password::broker();

        $status = $broker->sendResetLink($request->only('email'));

        return response()->json(
            ['message' => __($status)],
            $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT ? 200 : 422
        );
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $broker = Admin::where('email', $request->email)->exists()
            ? \Illuminate\Support\Facades\Password::broker('admins')
            : \Illuminate\Support\Facades\Password::broker();

        $status = $broker->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successfully. Please sign in.'])
            : response()->json(['message' => __($status), 'code' => 'reset_failed'], 422);
    }

    // == Helpers ===============================================================

    private function invalidCredentials(): JsonResponse
    {
        return response()->json(['message' => 'Invalid credentials. Please check your email/phone and password.'], 401);
    }

    /**
     * Generate + dispatch the 2FA login OTP (mirrors the web flow: gated by
     * site settings, delivered over SMS and/or email).
     */
    private function issueLoginOtp(User|Admin $user): array
    {
        SiteSetting::loadAll();
        $otpSms   = SiteSetting::get('otp_sms_enabled', '1') === '1';
        $otpEmail = SiteSetting::get('otp_email_enabled', '0') === '1';

        $sendSms   = $otpSms && !empty($user->phone);
        $sendEmail = $otpEmail && !empty($user->email);

        if ($sendSms || $sendEmail) {
            $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            $user->otp_code       = Hash::make($otp);
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();

            if ($sendSms) {
                (new SmsService())->send($user->phone, "MedRecords login code: {$otp}. Valid for 10 minutes. Do not share this code.");
            }
            if ($sendEmail) {
                try {
                    \Illuminate\Support\Facades\Mail::raw(
                        "Your MedRecords login code is: {$otp}\n\nThis code expires in 10 minutes. Do not share it with anyone.",
                        fn ($msg) => $msg->to($user->email)->subject('MedRecords Login Code')
                    );
                } catch (\Throwable $e) {
                    Log::warning("OTP email failed for user {$user->id}: " . $e->getMessage());
                }
            }
        }

        return array_values(array_filter(['sms' => $sendSms, 'email' => $sendEmail]));
    }

    private function tokenResponse(string $token, User|Admin $user, ?string $message = null, int $status = 200): JsonResponse
    {
        $body = [
            'access_token'       => $token,
            'token_type'         => 'bearer',
            'expires_in'         => (int) config('jwt.ttl', 60) * 60,
            'role'               => $user->role,
            'email_verified'     => $user->hasVerifiedEmail(),
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
            'user'               => $user,
        ];

        if ($message !== null) {
            $body['message'] = $message;
        }

        return response()->json($body, $status);
    }
}
