<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class RecordController extends Controller
{
    public function index(string $tab = 'all'): JsonResponse
    {
        $patient = $this->currentPatient()->load([
            'allergies', 'conditions',
            'encounters.facility', 'encounters.provider',
            'prescriptions.prescribedBy', 'prescriptions.dispenses',
            'vaccinations',
            'labResults',
            'dentalRecords.provider', 'dentalRecords.facility',
            'eyeCareRecords.provider', 'eyeCareRecords.facility',
            'procedures', 'hospitalizations', 'referrals',
        ]);

        return response()->json(['patient' => $patient, 'tab' => $tab]);
    }

    public function prescription(int $id): JsonResponse
    {
        $patient      = $this->currentPatient();
        $prescription = $patient->prescriptions()
            ->with(['prescribedBy', 'dispenses.facility', 'encounter'])
            ->findOrFail($id);

        return response()->json(['prescription' => $prescription, 'patient' => $patient]);
    }
}
