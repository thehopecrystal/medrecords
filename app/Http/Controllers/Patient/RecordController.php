<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class RecordController extends Controller
{
    public function index(string $tab = 'all')
    {
        $patient = $this->currentPatient()->load([
            'allergies', 'conditions',
            'encounters.facility', 'encounters.provider',
            'prescriptions.prescribedBy', 'prescriptions.dispenses',
            'activePrescriptions',
            'activeAllergies',
            'vaccinations.administeredBy',
            'labResults',
            'dentalRecords.provider', 'dentalRecords.facility',
            'eyeCareRecords.provider', 'eyeCareRecords.facility',
            'procedures', 'hospitalizations', 'referrals',
        ]);

        return Inertia::render('Patient/Record', compact('patient', 'tab'));
    }

    public function prescription(int $id)
    {
        $patient      = $this->currentPatient();
        $prescription = $patient->prescriptions()
            ->with(['prescribedBy', 'dispenses.facility', 'encounter'])
            ->findOrFail($id);

        return Inertia::render('Patient/PrescriptionDetail', compact('prescription', 'patient'));
    }
}
