<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\Referral;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function create(int $patientId)
    {
        $patient = Patient::with('user')->findOrFail($patientId);
        return view('provider.referral-new', compact('patient'));
    }

    public function store(Request $request, int $patientId)
    {
        $request->validate([
            'referred_to' => 'required|string|max:200',
            'specialty'   => 'required|string|max:100',
            'reason'      => 'required|string',
        ]);

        $patient  = Patient::findOrFail($patientId);
        $referral = Referral::create([
            'patient_id'           => $patient->id,
            'referring_provider_id'=> auth()->id(),
            'referred_to'          => $request->referred_to,
            'specialty'            => $request->specialty,
            'reason'               => $request->reason,
            'notes'                => $request->notes,
            'status'               => 'pending',
        ]);

        AuditLog::record($patient->id, auth()->id(), 'referral_created', "Referred to {$request->referred_to} ({$request->specialty})", 'Referral', $referral->id);

        return redirect()->route('provider.patient', $patient->id)
            ->with('success', "Referral to {$request->referred_to} created.");
    }
}
