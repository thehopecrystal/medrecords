<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Consent;
use App\Models\Notification;
use App\Models\Patient;
use Illuminate\Http\Request;

class ConsentRequestController extends Controller
{
    public function create(int $patientId)
    {
        $patient = Patient::with('user')->findOrFail($patientId);
        return view('provider.consent-request', compact('patient'));
    }

    public function store(Request $request, int $patientId)
    {
        $request->validate([
            'scope'      => 'required|string',
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

        $consent = Consent::create([
            'patient_id' => $patient->id,
            'grantee_id' => auth()->id(),
            'scope'      => $request->scope,
            'status'     => 'pending',
            'expires_at' => now()->addDays($request->expires_in),
        ]);

        // Notify the patient in-app
        Notification::create([
            'user_id' => $patient->user->id,
            'type'    => 'consent_request',
            'title'   => 'New consent request from ' . auth()->user()->name,
            'body'    => auth()->user()->name . ' is requesting ' . $consent->scopeLabel . ' access to your medical record for ' . $request->expires_in . ' days.',
            'data'    => ['consent_id' => $consent->id, 'scope' => $request->scope],
        ]);

        return redirect()->route('provider.patient', $patient->id)
            ->with('success', 'Consent request sent to patient.');
    }
}
