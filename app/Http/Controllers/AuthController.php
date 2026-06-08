<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
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
            return $this->redirectByRole(Auth::user()->role);
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
            'phone'      => ['required', 'string', 'regex:/^0[789][01]\d{8}$/', 'unique:users,phone'],
            'password'   => 'required|string|min:8|confirmed',
            'dob'        => 'nullable|date|before:today',
            'gender'     => 'nullable|in:male,female,other',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'genotype'   => 'nullable|in:AA,AS,SS,AC,SC',
            'state'      => 'nullable|string|max:60',
            'nin'        => 'nullable|regex:/^\d{11}$/|unique:users,nin',
        ], [
            'phone.regex'  => 'Phone must be a valid 11-digit Nigerian number (e.g. 08045678901).',
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

        $medId = 'MR-' . strtoupper(Str::random(4)) . '-'
               . rand(1000, 9999) . '-' . rand(1000, 9999);

        $patient = Patient::create([
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
