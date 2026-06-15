<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\AccessCode;
use App\Services\SmsService;
use Illuminate\Support\Facades\Crypt;

class DashboardController extends Controller
{
    public function welcome()
    {
        $patient = $this->currentPatient();
        return view('patient.welcome', compact('patient'));
    }

    public function index()
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

        return view('patient.dashboard', compact(
            'patient', 'recentActivity',
            'upcomingAppointments', 'latestVitals',
            'activeConsentCount'
        ));
    }

    public function showAccessCode()
    {
        $patient    = $this->currentPatient();
        $accessCode = AccessCode::where('patient_id', $patient->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        return view('patient.access-code', compact('accessCode', 'patient'));
    }

    public function generateCode()
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

        return view('patient.access-code', compact('accessCode', 'patient'));
    }

    public function sendSms(int $codeId)
    {
        $patient    = $this->currentPatient();
        $accessCode = AccessCode::where('patient_id', $patient->id)
            ->where('id', $codeId)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $phone = $this->currentUser()->phone;
        if (!$phone) {
            return back()->with('error', 'No phone number on your account.');
        }

        (new SmsService())->sendAccessCode($phone, $accessCode->decrypted_code, $patient->med_id);
        $accessCode->update(['sms_sent' => true]);

        return back()->with('success', "Access code sent to {$phone} via SMS.");
    }
}
