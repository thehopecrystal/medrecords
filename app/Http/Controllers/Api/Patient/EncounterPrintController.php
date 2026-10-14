<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Models\Encounter;

class EncounterPrintController extends Controller
{
    /** Returns the encounter sheet as a PDF download. */
    public function show(int $encounterId)
    {
        $patient = $this->currentPatient();

        $encounter = Encounter::with([
            'patient.user', 'patient.allergies',
            'provider', 'facility',
            'prescriptions', 'labResults', 'procedures',
        ])->where('patient_id', $patient->id)->findOrFail($encounterId);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('provider.encounter-print', compact('encounter'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('encounter-' . $encounter->id . '.pdf');
    }
}
