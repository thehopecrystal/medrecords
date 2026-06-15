<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BreakGlassEvent;
use App\Models\Consent;
use App\Models\Encounter;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
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
        $q        = $request->input('q', '');
        $patients = collect();

        if (strlen($q) >= 2) {
            // Only surface patients this provider already has an active consent for,
            // or search by MED-ID (which the patient must share out-of-band).
            $consentedIds = Consent::where('grantee_id', auth()->id())
                ->where('status', 'active')
                ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->pluck('patient_id');

            $patients = Patient::where(function ($query) use ($q) {
                    $query->whereHas('user', function ($uq) use ($q) {
                        $uq->where('name', 'like', "%{$q}%")
                           ->orWhere('phone', 'like', "%{$q}%")
                           ->orWhere('email', 'like', "%{$q}%");
                    })
                    ->orWhere('med_id', 'like', "%{$q}%");
                })
                ->whereIn('id', $consentedIds)
                ->with('user')
                ->take(20)
                ->get();
        }

        return view('provider.search', compact('patients', 'q'));
    }

    public function patient(int $id, string $tab = 'summary')
    {
        // Authorisation check BEFORE loading any sensitive data.
        $consent = Consent::where('patient_id', $id)
            ->where('grantee_id', auth()->id())
            ->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->first();

        $hasBreakGlass = BreakGlassEvent::where('patient_id', $id)
            ->where('provider_id', auth()->id())
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->exists();

        // Load base patient record for the consent-request flow.
        $patient = Patient::with('user')->findOrFail($id);

        if ($consent || $hasBreakGlass) {
            $patient->load([
                'allergies', 'conditions',
                'encounters.facility', 'encounters.provider',
                'prescriptions.prescribedBy',
                'labResults',
                'vaccinations',
            ]);
            AuditLog::record($id, auth()->id(), 'record_viewed', 'Provider viewed patient record', 'Patient', $id);
        } else {
            // No consent — hide all sensitive user fields; only name is needed for the consent-request UI.
            $patient->user->makeHidden(['email', 'phone', 'avatar', 'two_factor_enabled', 'last_login_at', 'email_verified_at']);
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
            'type'            => 'required|string|in:consultation,follow-up,emergency,procedure,telehealth,referral',
            'chief_complaint' => 'required|string|max:300',
            'history'         => 'nullable|string|max:5000',
            'diagnosis'       => 'nullable|string|max:2000',
            'icd10_code'      => ['nullable', 'string', 'max:10', 'regex:/^[A-Z][0-9][A-Z0-9](\.[A-Z0-9]{1,4})?$/i'],
            'vitals'          => 'nullable|json|max:2000',
            'assessment_plan' => 'required|string|max:5000',
            'notes'           => 'nullable|string|max:2000',
            // Optional inline prescription
            'drug_name'       => 'nullable|string|max:200',
            'drug_dose'       => 'nullable|string|max:100',
            'drug_frequency'  => 'nullable|string|max:100',
            'drug_duration'   => 'nullable|integer|min:1|max:365',
            'drug_instructions' => 'nullable|string|max:500',
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
            'icd10_code'      => $request->icd10_code ? strtoupper($request->icd10_code) : null,
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
                'rx_id'             => 'RX-' . strtoupper(Str::random(8)),
                'patient_id'        => $patient->id,
                'encounter_id'      => $encounter->id,
                'prescribed_by'     => auth()->id(),
                'drug_name'         => $request->drug_name,
                'dose'              => $request->drug_dose ?? '',
                'frequency'         => $request->drug_frequency ?? '',
                'duration_days'     => $request->drug_duration,
                'diagnosis_context' => $request->diagnosis,
                'instructions'      => $request->drug_instructions,
                'status'            => 'active',
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
            'reason_code'   => 'required|string|in:emergency,unconscious,critical,public_health,other',
            'justification' => 'required|string|min:30|max:1000',
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

        $providerId = (int) auth()->id();
        $provider   = $this->currentProvider();

        $event = new BreakGlassEvent([
            'reason_code'   => $request->reason_code,
            'justification' => $request->justification,
        ]);
        $event->patient_id  = $patient->id;
        $event->provider_id = $providerId;
        $event->status      = 'active';
        $event->expires_at  = now()->addHours(4);
        $event->save();

        AuditLog::record($patient->id, $providerId, 'break_glass', "Break-glass invoked: {$request->reason_code}", 'BreakGlassEvent', $event->id);

        // Notify the patient their record was accessed under emergency override.
        if ($patient->user_id) {
            Notification::send(
                $patient->user_id,
                'break_glass',
                'Emergency record access',
                auth()->user()->name . " accessed your medical record under an emergency override (" . $request->reason_code . "). This event has been logged and will be reviewed by your facility administrator.",
                ['event_id' => $event->id, 'reason_code' => $request->reason_code]
            );
        }

        // Notify the facility admin so they can review immediately.
        if ($provider->facility_id) {
            $adminUser = User::where('role', 'facility_admin')
                ->whereHas('provider', fn ($q) => $q->where('facility_id', $provider->facility_id))
                ->orWhere(function ($q) use ($provider) {
                    $q->where('role', 'facility_admin')
                      ->whereHas('facility', fn ($fq) => $fq->where('id', $provider->facility_id));
                })
                ->first();

            if ($adminUser) {
                Notification::send(
                    $adminUser->id,
                    'break_glass_review',
                    'Break-glass access requires review',
                    auth()->user()->name . " invoked emergency override for patient MED-ID {$patient->med_id}. Reason: {$request->reason_code}. Review and close this event in the admin dashboard.",
                    ['event_id' => $event->id, 'patient_id' => $patient->id]
                );
            }
        }

        return redirect()->route('provider.patient', $patient->id)
            ->with('success', 'Emergency access granted for 4 hours. Patient and administrator have been notified.');
    }
}
