<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit(): JsonResponse
    {
        return response()->json([
            'user'    => $this->currentUser(),
            'patient' => $this->currentPatient(),
        ]);
    }

    public function update(Request $request): JsonResponse
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

        return response()->json(['message' => 'Profile updated successfully.', 'user' => $user->fresh(), 'patient' => $patient->fresh()]);
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

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = $this->currentUser();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.', 'errors' => ['current_password' => ['Current password is incorrect.']]], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Invalidate every outstanding JWT for this user (web sessions too).
        \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Password changed successfully. All other active sessions have been signed out.']);
    }

    public function toggle2fa(Request $request): JsonResponse
    {
        return app(\App\Http\Controllers\Api\AccountController::class)->toggle2fa($request);
    }
}
