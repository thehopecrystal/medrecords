<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Encounter;

class EncounterPrintController extends Controller
{
    public function show(int $patientId, int $encounterId)
    {
        $this->requireConsent($patientId);

        $encounter = Encounter::with([
            'patient.user', 'patient.allergies',
            'provider', 'facility',
            'prescriptions', 'labResults', 'procedures',
        ])->where('patient_id', $patientId)->findOrFail($encounterId);

        return view('provider.encounter-print', compact('encounter'));
    }
}
