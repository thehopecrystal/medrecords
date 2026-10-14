<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Models\AccessCode;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;

class DashboardController extends Controller
{
    public function welcome(): JsonResponse
    {
        return response()->json(['patient' => $this->currentPatient()]);
    }

    public function index(): JsonResponse
    {
        $patient = $this->currentPatient();

        $patient->load(['conditions', 'allergies', 'prescriptions']);

        $recentActivity       = $patient->auditLogs()->with('actor')->latest()->take(5)->get();
        $upcomingAppointments = $patient->appointments()->upcoming()->with('provider', 'facility')->take(4)->get();
        $latestVitals         = $patient->vitalsLogs()->latest('logged_at')->first();
        $activeConsentCount   = $patient->consents()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        return response()->json([
            'patient'               => $patient,
            'recent_activity'       => $recentActivity,
            'upcoming_appointments' => $upcomingAppointments,
            'latest_vitals'         => $latestVitals,
            'active_consent_count'  => $activeConsentCount,
        ]);
    }

    public function showAccessCode(): JsonResponse
    {
        $patient    = $this->currentPatient();
        $accessCode = AccessCode::where('patient_id', $patient->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        return response()->json(['patient' => $patient, 'access_code' => $accessCode]);
    }

    public function generateCode(): JsonResponse
    {
        $patient = $this->currentPatient();

        AccessCode::where('patient_id', $patient->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->update(['expires_at' => now()]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $accessCode = AccessCode::create([
            'patient_id' => $patient->id,
            'code'       => Crypt::encryptString($code),
            'expires_at' => now()->addMinutes(10),
            'sms_sent'   => false,
        ]);

        return response()->json([
            'message'     => 'New access code generated. It expires in 10 minutes.',
            'patient'     => $patient,
            'access_code' => $accessCode,
        ], 201);
    }

    public function sendSms(int $codeId): JsonResponse
    {
        $patient    = $this->currentPatient();
        $accessCode = AccessCode::where('patient_id', $patient->id)
            ->where('id', $codeId)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $phone = $this->currentUser()->phone;
        if (!$phone) {
            return response()->json(['message' => 'No phone number on your account.'], 422);
        }

        (new SmsService())->sendAccessCode($phone, $accessCode->decrypted_code, $patient->med_id);
        $accessCode->update(['sms_sent' => true]);

        return response()->json(['message' => "Access code sent to {$phone} via SMS."]);
    }
}
