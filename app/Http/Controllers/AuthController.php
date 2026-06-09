<?php

namespace App\Http\Controllers;

use App\Models\Patient;
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

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $credentials = [$field => $request->login, 'password' => $request->password];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['login' => 'Your account has been deactivated. Please contact support.'])->withInput();
            }

            // 2FA check
            if ($user->two_factor_enabled && $user->phone) {
                $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->update(['otp_code' => Hash::make($otp), 'otp_expires_at' => now()->addMinutes(10)]);
                $userId = $user->id;
                Auth::logout();
                // Regenerate session after logout so the pre-OTP session ID differs
                // from both the unauthenticated and authenticated session IDs.
                $request->session()->regenerate();
                $request->session()->put('2fa_user_id', $userId);
                (new SmsService())->send($user->phone, "MedRecords login code: {$otp}. Valid for 10 minutes. Do not share this code.");
                return redirect()->route('auth.otp');
            }

            $user->update(['last_login_at' => now()]);
            return $this->redirectByRole($user->role);
        }

        return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
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
            'phone.regex'  => 'Phone number must include a country code (e.g. +2348012345678).',
            'nin.regex'    => 'NIN must be exactly 11 digits.',
            'nin.unique'   => 'This NIN is unavailable.',
            'email.unique' => 'This email address is unavailable.',
            'phone.unique' => 'This phone number is unavailable.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'nin'      => $request->nin ?: null,
            'role'     => 'patient',
            'password' => Hash::make($request->password),
        ]);

        $medId = 'MR-' . strtoupper(Str::random(6)) . '-' . strtoupper(Str::random(6));

        Patient::create([
            'user_id'    => $user->id,
            'med_id'     => $medId,
            'dob'        => $request->dob ?: null,
            'gender'     => $request->gender ?: null,
            'blood_type' => $request->blood_type ?: null,
            'genotype'   => $request->genotype ?: null,
            'state'      => $request->state ?: null,
        ]);

        Auth::login($user);

        return redirect()->route('patient.welcome');
    }

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

        $user = User::findOrFail($userId);

        if (!Hash::check($request->otp, $user->otp_code ?? '') || now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Invalid or expired code. Try again.']);
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

    private function redirectByRole(string $role)
    {
        return match($role) {
            'patient'                    => redirect()->route('patient.dashboard'),
            'provider'                   => redirect()->route('provider.dashboard'),
            'pharmacist'                 => redirect()->route('pharmacy.dashboard'),
            'facility_admin', 'super_admin' => redirect()->route('admin.dashboard'),
            default                      => redirect('/'),
        };
    }
}
