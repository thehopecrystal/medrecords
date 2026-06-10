<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Encounter;
use Inertia\Inertia;

class EncounterPrintController extends Controller
{
    public function show(int $encounterId)
    {
        $patient = $this->currentPatient();

        $encounter = Encounter::with([
            'patient.user', 'patient.allergies',
            'provider', 'facility',
            'prescriptions', 'labResults', 'procedures',
        ])->where('patient_id', $patient->id)->findOrFail($encounterId);

        return Inertia::render('Provider/EncounterPrint', compact('encounter'));
    }
}
