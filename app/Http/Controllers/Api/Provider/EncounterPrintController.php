<?php

namespace App\Http\Controllers\Api\Provider;

use App\Http\Controllers\Controller;
use App\Models\Encounter;

class EncounterPrintController extends Controller
{
    /** Encounter sheet as a PDF download (consent-gated like the web flow). */
    public function show(int $patientId, int $encounterId)
    {
        $this->requireConsent($patientId);

        $encounter = Encounter::with([
            'patient.user', 'patient.allergies',
            'provider', 'facility',
            'prescriptions', 'labResults', 'procedures',
        ])->where('patient_id', $patientId)->findOrFail($encounterId);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('provider.encounter-print', compact('encounter'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('encounter-' . $encounter->id . '.pdf');
    }
}
