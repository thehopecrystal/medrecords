<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Dependent;

class FamilySummaryController extends Controller
{
    public function index()
    {
        $patient    = $this->currentPatient();
        $dependents = $patient->dependents()
            ->with(['prescriptions' => fn($q) => $q->where('status', 'active'),
                    'vaccinations', 'conditions' => fn($q) => $q->where('status', 'active'),
                    'encounters' => fn($q) => $q->latest()->limit(1)])
            ->get();

        // Include self in summary
        $self = $patient->load([
            'prescriptions' => fn($q) => $q->where('status', 'active'),
            'vaccinations',
            'conditions'    => fn($q) => $q->where('status', 'active'),
            'encounters'    => fn($q) => $q->latest()->limit(1),
        ]);

        return view('patient.family-summary', compact('patient', 'dependents', 'self'));
    }
}
