<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
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
            $field = 'username';
        }

        $credentials = [$field => $request->login, 'password' => $request->password];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['login' => 'Your account has been deactivated. Please contact support.'])->withInput();
            }

            if ($user->two_factor_enabled && $user->phone) {
                $otp    = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
                $userId = $user->id;
                $user->update(['otp_code' => Hash::make($otp), 'otp_expires_at' => now()->addMinutes(10)]);
                Auth::logout();
                // Invalidate (wipe all session data) then regenerate ID — prevents session fixation.
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                $request->session()->put('2fa_user_id', $userId);
                (new SmsService())->send($user->phone, "MedRecords login code: {$otp}. Valid for 10 minutes. Do not share this code.");
                return redirect()->route('auth.otp');
            }

            $user->update(['last_login_at' => now()]);
            return $this->redirectByRole($user->role);
        }

        return back()->withErrors(['login' => 'Invalid credentials. Please check your email/phone and password.'])->withInput();
    }

    // ── Registration ─────────────────────────────────────────────────────────

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        return match ($request->input('reg_type', 'patient')) {
            'provider'          => $this->registerProvider($request),
            'hospital', 'clinic' => $this->registerOrg($request, $request->input('reg_type')),
            'pharmacy'          => $this->registerOrg($request, 'pharmacy'),
            default             => $this->registerPatient($request),
        };
    }

    // ── Patient ───────────────────────────────────────────────────────────────

    private function registerPatient(Request $request)
    {
        $request->validate([
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
            'role'     => 'patient',
            'password' => Hash::make($request->password),
        ]);

        Patient::create([
            'user_id'    => $user->id,
            'med_id'     => 'MR-' . strtoupper(Str::random(6)) . '-' . strtoupper(Str::random(6)),
            'dob'        => $request->dob ?: null,
            'gender'     => $request->gender ?: null,
            'blood_type' => $request->blood_type ?: null,
            'genotype'   => $request->genotype ?: null,
            'state'      => $request->state ?: null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        return redirect()->route('patient.welcome');
    }

    // ── Provider / Doctor ─────────────────────────────────────────────────────

    private function registerProvider(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:120',
            'email'      => 'required|email|unique:users,email',
            'phone'      => ['required', 'string', 'regex:/^\+[1-9]\d{7,14}$/', 'unique:users,phone'],
            'password'   => 'required|string|min:8|confirmed',
            'specialty'  => 'required|string|max:120',
            'license_no' => 'nullable|string|max:50',
            'mdcn_folio' => 'nullable|string|max:50',
        ], [
            'phone.regex'     => 'Phone must include a country code (e.g. +2348012345678).',
            'email.unique'    => 'This email address is already registered.',
            'phone.unique'    => 'This phone number is already registered.',
            'specialty.required' => 'Please enter your specialty.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => 'provider',
            'password' => Hash::make($request->password),
        ]);

        Provider::create([
            'user_id'     => $user->id,
            'facility_id' => null,
            'specialty'   => $request->specialty,
            'license_no'  => $request->license_no ?: null,
            'mdcn_folio'  => $request->mdcn_folio ?: null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        return redirect()->route('provider.dashboard')->with('onboarding', true);
    }

    // ── Organisation (hospital / clinic / pharmacy) ───────────────────────────

    private function registerOrg(Request $request, string $type)
    {
        $facilityTypes = ['hospital' => 'hospital', 'clinic' => 'clinic', 'pharmacy' => 'pharmacy'];
        $facilityType  = $facilityTypes[$type] ?? 'hospital';

        $request->validate([
            'name'        => 'required|string|max:120',
            'email'       => 'required|email|unique:users,email',
            'phone'       => ['required', 'string', 'regex:/^\+[1-9]\d{7,14}$/', 'unique:users,phone'],
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
            'phone.regex'   => 'Phone must include a country code (e.g. +2348012345678).',
            'email.unique'  => 'This email address is already registered.',
            'phone.unique'  => 'This phone number is already registered.',
            'org_name.required' => 'Please enter your organisation name.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => 'facility_admin',
            'password' => Hash::make($request->password),
        ]);

        Facility::create([
            'owner_user_id' => $user->id,
            'name'          => $request->org_name,
            'type'          => $request->org_type ?: $facilityType,
            'license_no'    => $request->org_license ?: null,
            'address'       => $request->org_address ?: null,
            'city'          => $request->org_city ?: null,
            'state'         => $request->org_state ?: null,
            'phone'         => $request->org_phone ?: null,
            'email'         => $request->org_email ?: null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        return redirect()->route('admin.dashboard')->with('onboarding', true);
    }

    // ── OTP ───────────────────────────────────────────────────────────────────

    public function showOtp(Request $request)
    {
        if (!$request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string|digits:6']);

        $userId = $request->session()->get('2fa_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['otp' => 'Session expired. Please log in again.']);
        }

        $user = User::find($userId);
        if (!$user) {
            $request->session()->invalidate();
            return redirect()->route('login')->withErrors(['otp' => 'Session expired. Please log in again.']);
        }

        $expired  = !$user->otp_expires_at || now()->isAfter($user->otp_expires_at);
        $mismatch = !Hash::check($request->otp, $user->otp_code ?? '');

        if ($expired || $mismatch) {
            // Wipe code immediately — one-use guarantee even on failure.
            if ($expired) {
                $user->update(['otp_code' => null, 'otp_expires_at' => null]);
            }
            return back()->withErrors(['otp' => $expired
                ? 'Code has expired. Please log in again.'
                : 'Invalid code. Please try again.']);
        }

        $user->update(['otp_code' => null, 'otp_expires_at' => null, 'last_login_at' => now()]);
        $request->session()->forget('2fa_user_id');

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectByRole($user->role);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'patient'                        => redirect()->route('patient.dashboard'),
            'provider'                       => redirect()->route('provider.dashboard'),
            'pharmacist'                     => redirect()->route('pharmacy.dashboard'),
            'facility_admin', 'super_admin'  => redirect()->route('admin.dashboard'),
            default                          => redirect('/'),
        };
    }
}
