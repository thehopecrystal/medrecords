<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Encounter;
use Inertia\Inertia;

class EncounterPrintController extends Controller
{
    public function show(int $patientId, int $encounterId)
    {
        $encounter = Encounter::with([
            'patient.user', 'patient.allergies',
            'provider', 'facility',
            'prescriptions', 'labResults', 'procedures',
        ])->where('patient_id', $patientId)->findOrFail($encounterId);

        return Inertia::render('Provider/EncounterPrint', compact('encounter'));
    }
}
