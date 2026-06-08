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
        $this->requireConsent($patientId);
        $patient = Patient::with('user')->findOrFail($patientId);
        return view('provider.referral-new', compact('patient'));
    }

    public function store(Request $request, int $patientId)
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

        return redirect()->route('provider.patient', $patient->id)
            ->with('success', "Referral to {$request->referred_to} created.");
    }

    public function index()
    {
        $provider  = $this->currentProvider();
        $referrals = Referral::with(['patient.user'])
            ->where('referring_provider_id', $provider->user_id)
            ->latest()->paginate(20);

        return view('provider.referrals', compact('referrals'));
    }

    public function updateStatus(int $id, Request $request)
    {
        $provider = $this->currentProvider();
        $referral = Referral::where('referring_provider_id', $provider->user_id)->findOrFail($id);

        $request->validate(['status' => 'required|in:pending,accepted,completed,declined']);
        $referral->update(['status' => $request->status]);

        return back()->with('success', 'Referral status updated.');
    }
}
