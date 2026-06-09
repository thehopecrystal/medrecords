<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BreakGlassEvent;
use App\Models\Consent;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkstationController extends Controller
{
    public function dashboard()
    {
        $provider = $this->currentProvider();
        $recentEncounters = Encounter::where('provider_id', auth()->id())
            ->with(['patient.user', 'facility'])
            ->latest()
            ->take(10)
            ->get();

        return view('provider.dashboard', compact('provider', 'recentEncounters'));
    }

    public function search(Request $request)
    {
        $q = $request->input('q', '');
        $patients = collect();

        if (strlen($q) >= 2) {
            $patients = Patient::whereHas('user', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->orWhere('med_id', 'like', "%{$q}%")
            ->with('user')
            ->take(20)
            ->get();
        }

        return view('provider.search', compact('patients', 'q'));
    }

    public function patient(int $id, string $tab = 'summary')
    {
        $patient = Patient::with(['user', 'allergies', 'conditions'])->findOrFail($id);

        $consent = Consent::where('patient_id', $id)
            ->where('grantee_id', auth()->id())
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->first();

        $hasBreakGlass = BreakGlassEvent::where('patient_id', $id)
            ->where('provider_id', auth()->id())
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->exists();

        if ($consent || $hasBreakGlass) {
            $patient->load([
                'encounters.facility', 'encounters.provider',
                'prescriptions.prescribedBy',
                'labResults',
                'vaccinations',
            ]);
            AuditLog::record($id, auth()->id(), 'record_viewed', 'Provider viewed patient record', 'Patient', $id);
        }

        return view('provider.patient', compact('patient', 'consent', 'tab'));
    }

    public function newEncounter(int $patientId)
    {
        $this->requireConsent($patientId);

        $patient  = Patient::with('user')->findOrFail($patientId);
        $provider = $this->currentProvider();

        return view('provider.encounter-new', compact('patient', 'provider'));
    }

    public function storeEncounter(Request $request, int $patientId)
    {
        $this->requireConsent($patientId);

        $request->validate([
            'type'            => 'required|string',
            'chief_complaint' => 'required|string|max:300',
            'assessment_plan' => 'required|string',
        ]);

        $patient = Patient::findOrFail($patientId);

        $encounter = Encounter::create([
            'patient_id'      => $patient->id,
            'provider_id'     => auth()->id(),
            'facility_id'     => $this->currentProvider()->facility_id,
            'type'            => $request->type,
            'chief_complaint' => $request->chief_complaint,
            'history'         => $request->history,
            'diagnosis'       => $request->diagnosis,
            'icd10_code'      => $request->icd10_code,
            'vitals'          => $request->vitals,
            'assessment_plan' => $request->assessment_plan,
            'notes'           => $request->notes,
            'status'          => $request->has('sign') ? 'signed' : 'draft',
            'signed_at'       => $request->has('sign') ? now() : null,
        ]);

        if ($request->has('sign')) {
            AuditLog::record($patient->id, auth()->id(), 'encounter_signed', "Encounter signed: {$request->chief_complaint}", 'Encounter', $encounter->id);
        }

        if ($request->filled('drug_name')) {
            Prescription::create([
                'rx_id'              => 'RX-' . strtoupper(Str::random(8)),
                'patient_id'         => $patient->id,
                'encounter_id'       => $encounter->id,
                'prescribed_by'      => auth()->id(),
                'drug_name'          => $request->drug_name,
                'dose'               => $request->drug_dose ?? '',
                'frequency'          => $request->drug_frequency ?? '',
                'duration_days'      => $request->drug_duration,
                'diagnosis_context'  => $request->diagnosis,
                'instructions'       => $request->drug_instructions,
                'status'             => 'active',
            ]);

            AuditLog::record($patient->id, auth()->id(), 'prescription_created', "Prescribed {$request->drug_name}", 'Prescription', null);
        }

        return redirect()->route('provider.patient', $patient->id)
            ->with('success', 'Encounter ' . ($request->has('sign') ? 'signed and saved.' : 'saved as draft.'));
    }

    public function breakGlass(int $patientId)
    {
        $patient = Patient::with('user')->findOrFail($patientId);
        return view('provider.break-glass', compact('patient'));
    }

    public function storeBreakGlass(Request $request, int $patientId)
    {
        $request->validate([
            'reason_code'   => 'required|string',
            'justification' => 'required|string|min:20',
        ]);

        $patient = Patient::findOrFail($patientId);

        abort_if(
            BreakGlassEvent::where('patient_id', $patient->id)
                ->where('provider_id', auth()->id())
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->exists(),
            409,
            'You already have an active emergency access session for this patient.'
        );

        $event = BreakGlassEvent::create([
            'patient_id'    => $patient->id,
            'provider_id'   => auth()->id(),
            'reason_code'   => $request->reason_code,
            'justification' => $request->justification,
            'status'        => 'active',
            'expires_at'    => now()->addHours(24),
        ]);

        AuditLog::record($patient->id, auth()->id(), 'break_glass', "Break-glass invoked: {$request->reason_code}", 'BreakGlassEvent', $event->id);

        return redirect()->route('provider.patient', $patient->id)
            ->with('success', 'Emergency access granted for 24 hours. Patient has been notified.');
    }
}
