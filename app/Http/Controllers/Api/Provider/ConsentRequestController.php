<?php

namespace App\Http\Controllers\Api\Provider;

use App\Http\Controllers\Controller;
use App\Models\Consent;
use App\Models\Notification;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsentRequestController extends Controller
{
    /** Consent-request form context. */
    public function create(int $patientId): JsonResponse
    {
        $patient = Patient::with('user')->findOrFail($patientId);

        return response()->json([
            'patient' => $patient,
            'fields'  => [
                'scope'      => 'required|in:full,encounter,prescription,lab,dental,eye_care,vaccination,dispense,custom',
                'expires_in' => 'required|integer|min:1|max:365 (days)',
            ],
        ]);
    }

    public function store(Request $request, int $patientId): JsonResponse
    {
        $request->validate([
            'scope'      => 'required|in:full,encounter,prescription,lab,dental,eye_care,vaccination,dispense,custom',
            'expires_in' => 'required|integer|min:1|max:365',
        ]);

        $patient = Patient::findOrFail($patientId);

        abort_if(
            Consent::where('patient_id', $patient->id)
                ->where('grantee_id', auth()->id())
                ->where('status', 'pending')
                ->exists(),
            409,
            'You already have a pending consent request with this patient.'
        );

        $consent = new Consent([
            'scope'      => $request->scope,
            'status'     => 'pending',
            'expires_at' => now()->addDays($request->expires_in),
        ]);
        $consent->patient_id = $patient->id;
        $consent->grantee_id = (int) auth()->id();
        $consent->save();

        // Notify the patient in-app
        Notification::create([
            'user_id' => $patient->user->id,
            'type'    => 'consent_request',
            'title'   => 'New consent request from ' . auth()->user()->name,
            'body'    => auth()->user()->name . ' is requesting ' . $consent->scopeLabel . ' access to your medical record for ' . $request->expires_in . ' days.',
            'data'    => ['consent_id' => $consent->id, 'scope' => $request->scope],
        ]);

        return response()->json(['message' => 'Consent request sent to patient.', 'consent' => $consent], 201);
    }
}
