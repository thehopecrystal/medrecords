<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\AccessCode;
use App\Services\SmsService;

class DashboardController extends Controller
{
    public function welcome()
    {
        $patient = $this->currentPatient();
        return view('patient.welcome', compact('patient'));
    }

    public function index()
    {
        $patient        = $this->currentPatient();
        $recentActivity = $patient->auditLogs()->with('actor')->latest()->take(10)->get();

        return view('patient.dashboard', compact('patient', 'recentActivity'));
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
            'code'       => $code,
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

        (new SmsService())->sendAccessCode($phone, $accessCode->code, $patient->med_id);
        $accessCode->update(['sms_sent' => true]);

        return back()->with('success', "Access code sent to {$phone} via SMS.");
    }
}
