<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Inertia\Inertia;

class ConsentController extends Controller
{
    public function index()
    {
        $patient = $this->currentPatient();
        $pending = $patient->consents()->with('grantee')->where('status', 'pending')->latest()->get();
        $active  = $patient->consents()->with('grantee')->where('status', 'active')->get();
        $expired = $patient->consents()->with('grantee')->where('status', 'expired')->latest()->take(20)->get();
        $revoked = $patient->consents()->with('grantee')->where('status', 'revoked')->latest()->take(20)->get();

        return Inertia::render('Patient/Consents', compact('patient', 'pending', 'active', 'expired', 'revoked'));
    }

    public function revoke(int $id)
    {
        $patient = $this->currentPatient();
        $consent = $patient->consents()->findOrFail($id);

        $consent->update(['status' => 'revoked', 'revoked_at' => now()]);

        AuditLog::record($patient->id, auth()->id(), 'consent_revoked', "Revoked consent for {$consent->grantee->name}");

        return back()->with('success', 'Consent revoked.');
    }
}
