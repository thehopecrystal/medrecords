<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class FamilySummaryController extends Controller
{
    public function index(): JsonResponse
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

        return response()->json(['dependents' => $dependents, 'self' => $self]);
    }
}
