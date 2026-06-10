<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Dependent;
use Inertia\Inertia;

class FamilySummaryController extends Controller
{
    public function index()
    {
        $patient    = $this->currentPatient();
        $dependents = $patient->dependents()
            ->with(['dependent' => fn($q) => $q->with([
                'user',
                'prescriptions' => fn($q) => $q->where('status', 'active'),
                'vaccinations',
                'conditions'    => fn($q) => $q->where('status', 'active'),
                'encounters'    => fn($q) => $q->latest()->limit(1),
            ])])
            ->get();

        // Include self in summary
        $self = $patient->load([
            'prescriptions' => fn($q) => $q->where('status', 'active'),
            'vaccinations',
            'conditions'    => fn($q) => $q->where('status', 'active'),
            'encounters'    => fn($q) => $q->latest()->limit(1),
        ]);

        return Inertia::render('Patient/FamilySummary', compact('patient', 'dependents', 'self'));
    }
}
