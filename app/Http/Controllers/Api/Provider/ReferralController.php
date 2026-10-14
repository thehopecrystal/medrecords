<?php

namespace App\Http\Controllers\Api\Provider;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\Referral;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /** Referral form context. */
    public function create(int $patientId): JsonResponse
    {
        $this->requireConsent($patientId);
        $patient = Patient::with('user')->findOrFail($patientId);

        return response()->json(['patient' => $patient]);
    }

    public function store(Request $request, int $patientId): JsonResponse
    {
        $this->requireConsent($patientId);
        $request->validate([
            'referred_to' => 'required|string|max:200',
            'specialty'   => 'required|string|max:100',
            'reason'      => 'required|string|max:500',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $patient  = Patient::findOrFail($patientId);
        $referral = Referral::create([
            'patient_id'            => $patient->id,
            'referring_provider_id' => auth()->id(),
            'referred_to'           => $request->referred_to,
            'specialty'             => $request->specialty,
            'reason'                => $request->reason,
            'notes'                 => $request->notes,
            'status'                => 'pending',
        ]);

        AuditLog::record($patient->id, auth()->id(), 'referral_created', "Referred to {$request->referred_to} ({$request->specialty})", 'Referral', $referral->id);

        return response()->json(['message' => "Referral to {$request->referred_to} created.", 'referral' => $referral], 201);
    }

    public function index(): JsonResponse
    {
        $provider  = $this->currentProvider();
        $referrals = Referral::with(['patient.user'])
            ->where('referring_provider_id', $provider->user_id)
            ->latest()->paginate(20);

        return response()->json(['referrals' => $referrals]);
    }

    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $provider = $this->currentProvider();
        $referral = Referral::where('referring_provider_id', $provider->user_id)->findOrFail($id);

        $request->validate(['status' => 'required|in:pending,accepted,completed,declined']);
        $referral->update(['status' => $request->status]);

        return response()->json(['message' => 'Referral status updated.', 'referral' => $referral]);
    }
}
