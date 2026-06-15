<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $patient = $this->currentPatient();
        $user    = $this->currentUser();
        return view('patient.profile', compact('patient', 'user'));
    }

    public function update(Request $request)
    {
        $user    = $this->currentUser();
        $patient = $this->currentPatient();
        $tab     = $request->input('tab', 'personal');

        match ($tab) {
            'personal'  => $this->updatePersonal($request, $user, $patient),
            'medical'   => $this->updateMedical($request, $patient),
            'emergency' => $this->updateEmergency($request, $patient),
            default     => abort(422, 'Unknown profile tab.'),
        };

        return back()->with('success', 'Profile updated successfully.');
    }

    private function updatePersonal($request, $user, $patient): void
    {
        $request->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'phone'   => ['required', 'regex:/^\+[1-9]\d{7,14}$/', 'unique:users,phone,' . $user->id],
            'dob'     => 'nullable|date|before:today',
            'gender'  => 'nullable|in:male,female,other',
            'state'   => 'nullable|string|max:60',
            'city'    => 'nullable|string|max:60',
            'address' => 'nullable|string|max:255',
        ], [
            'phone.regex' => 'Phone number must include a country code (e.g. +2348012345678).',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        $patient->update([
            'dob'     => $request->dob ?: null,
            'gender'  => $request->gender ?: null,
            'state'   => $request->state ?: null,
            'city'    => $request->city ?: null,
            'address' => $request->address ?: null,
        ]);
    }

    private function updateMedical($request, $patient): void
    {
        $request->validate([
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'genotype'   => 'nullable|in:AA,AS,SS,AC,SC',
        ]);

        $patient->update([
            'blood_type' => $request->blood_type ?: null,
            'genotype'   => $request->genotype ?: null,
        ]);
    }

    private function updateEmergency($request, $patient): void
    {
        $request->validate([
            'emergency_contact_name'  => 'nullable|string|max:120',
            'emergency_contact_phone' => ['nullable', 'regex:/^\+[1-9]\d{7,14}$/'],
        ]);

        $patient->update([
            'emergency_contact_name'  => $request->emergency_contact_name ?: null,
            'emergency_contact_phone' => $request->emergency_contact_phone ?: null,
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = $this->currentUser();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Invalidate all other active sessions so stolen sessions can't be reused.
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        $request->session()->regenerate();

        return back()->with('success', 'Password changed successfully. All other active sessions have been signed out.');
    }

    public function toggle2fa(Request $request)
    {
        $user    = $this->currentUser();
        $enabled = !$user->two_factor_enabled;

        if ($enabled && !$user->phone) {
            return back()->with('error', 'A verified phone number is required to enable two-factor authentication.');
        }

        if (!$enabled) {
            $request->validate(['current_password' => 'required']);
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
        }

        $user->two_factor_enabled = $enabled;
        $user->save();

        return back()->with('success', $enabled
            ? 'Two-factor authentication enabled. You will receive an SMS code on each login.'
            : 'Two-factor authentication disabled.');
    }
}
