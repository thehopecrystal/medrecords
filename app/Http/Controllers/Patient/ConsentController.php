<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;

class ConsentController extends Controller
{
    public function index()
    {
        $patient = $this->currentPatient();
        $pending = $patient->consents()->with('grantee')->where('status', 'pending')->latest()->get();
        $active  = $patient->consents()->with('grantee')->where('status', 'active')->get();
        $expired = $patient->consents()->with('grantee')->where('status', 'expired')->latest()->take(20)->get();
        $revoked = $patient->consents()->with('grantee')->where('status', 'revoked')->latest()->take(20)->get();

        return view('patient.consents', compact('patient', 'pending', 'active', 'expired', 'revoked'));
    }

    public function revoke(int $id)
    {
        $patient = $this->currentPatient();
        $consent = $patient->consents()->findOrFail($id);

        $consent->update(['status' => 'revoked', 'revoked_at' => now()]);

        AuditLog::record($patient->id, (int) auth()->id(), 'consent_revoked', "Revoked consent for {$consent->grantee->name}");

        return back()->with('success', 'Consent revoked.');
    }

    public function renew(int $id)
    {
        $patient = $this->currentPatient();
        $consent = $patient->consents()->with('grantee')->findOrFail($id);

        abort_if(!in_array($consent->status, ['active', 'expired']), 422, 'Only active or expired consents can be renewed.');

        $consent->update([
            'status'     => 'active',
            'expires_at' => now()->addDays(90),
            'revoked_at' => null,
        ]);

        AuditLog::record($patient->id, (int) auth()->id(), 'consent_renewed', "Renewed consent for {$consent->grantee->name} (90 days)");

        return back()->with('success', 'Consent renewed for 90 days.');
    }
}
