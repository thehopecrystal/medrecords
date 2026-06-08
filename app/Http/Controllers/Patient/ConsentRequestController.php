<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Consent;
use App\Models\Notification;

class ConsentRequestController extends Controller
{
    public function approve(int $id)
    {
        $patient = $this->currentPatient();
        $consent = Consent::where('patient_id', $patient->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $consent->update(['status' => 'active', 'granted_at' => now()]);

        Notification::where('user_id', auth()->id())
            ->whereJsonContains('data->consent_id', $id)
            ->update(['read_at' => now()]);

        AuditLog::record($patient->id, auth()->id(), 'consent_granted', "Consent granted to {$consent->grantee->name} — {$consent->scopeLabel}");

        return back()->with('success', 'Consent granted to ' . $consent->grantee->name . '.');
    }

    public function decline(int $id)
    {
        $patient = $this->currentPatient();
        $consent = Consent::where('patient_id', $patient->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $grantee = $consent->grantee->name;
        $consent->delete();

        Notification::where('user_id', auth()->id())
            ->whereJsonContains('data->consent_id', $id)
            ->update(['read_at' => now()]);

        AuditLog::record($patient->id, auth()->id(), 'consent_declined', "Consent request from {$grantee} was declined");

        return back()->with('success', 'Consent request declined.');
    }
}
