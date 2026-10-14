<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Consent;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class ConsentController extends Controller
{
    public function index(): JsonResponse
    {
        $patient = $this->currentPatient();
        $pending = $patient->consents()->with('grantee')->where('status', 'pending')->latest()->get();
        $active  = $patient->consents()->with('grantee')->where('status', 'active')->get();
        $expired = $patient->consents()->with('grantee')->where('status', 'expired')->latest()->take(20)->get();
        $revoked = $patient->consents()->with('grantee')->where('status', 'revoked')->latest()->take(20)->get();

        return response()->json(compact('pending', 'active', 'expired', 'revoked'));
    }

    public function revoke(int $id): JsonResponse
    {
        $patient = $this->currentPatient();
        $consent = $patient->consents()->findOrFail($id);

        $consent->update(['status' => 'revoked', 'revoked_at' => now()]);

        AuditLog::record($patient->id, (int) auth()->id(), 'consent_revoked', "Revoked consent for {$consent->grantee->name}");

        return response()->json(['message' => 'Consent revoked.', 'consent' => $consent]);
    }

    public function renew(int $id): JsonResponse
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

        return response()->json(['message' => 'Consent renewed for 90 days.', 'consent' => $consent]);
    }

    /** Mirrors Patient\ConsentRequestController (approve a provider's request). */
    public function approve(int $id): JsonResponse
    {
        $patient = $this->currentPatient();
        $consent = Consent::where('patient_id', $patient->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $consent->update(['status' => 'active', 'granted_at' => now()]);

        Notification::where('user_id', auth()->id())
            ->whereJsonContains('data->consent_id', $id)
            ->update(['read_at' => now()]);

        AuditLog::record($patient->id, auth()->id(), 'consent_granted', "Consent granted to {$consent->grantee->name} - {$consent->scopeLabel}");

        return response()->json(['message' => 'Consent granted to ' . $consent->grantee->name . '.', 'consent' => $consent]);
    }

    public function decline(int $id): JsonResponse
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

        return response()->json(['message' => 'Consent request declined.']);
    }
}
