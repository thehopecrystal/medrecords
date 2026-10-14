<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Models\Dependent;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DependentController extends Controller
{
    public function index(): JsonResponse
    {
        $patient    = $this->currentPatient();
        $dependents = $patient->dependents()->with([
            'dependent.user',
            'dependent.vaccinations',
            'dependent.prescriptions',
        ])->get();

        return response()->json(['patient' => $patient, 'dependents' => $dependents]);
    }

    /** Form metadata for the web "add family member" screen. */
    public function create(): JsonResponse
    {
        return response()->json([
            'fields' => [
                'name'         => 'required|string|max:120',
                'dob'          => 'required|date|before:today',
                'gender'       => 'required|in:male,female,other',
                'relationship' => 'required|string|max:60',
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'         => 'required|string|max:120',
            'dob'          => 'required|date|before:today',
            'gender'       => 'required|in:male,female,other',
            'relationship' => 'required|string|max:60',
        ]);

        $guardian = $this->currentPatient();

        $depUser = User::create([
            'name'     => $request->name,
            'email'    => 'dep_' . Str::random(8) . '@medrecords.internal',
            'password' => Hash::make(Str::random(32)),
        ]);
        $depUser->role = 'patient';
        $depUser->save();

        $depPatient = new Patient([
            'med_id' => 'MR-' . strtoupper(Str::random(6)) . '-' . strtoupper(Str::random(6)),
            'dob'    => $request->dob,
            'gender' => $request->gender,
        ]);
        $depPatient->user_id = $depUser->id;
        $depPatient->save();

        $dependent = Dependent::create([
            'guardian_patient_id'  => $guardian->id,
            'dependent_patient_id' => $depPatient->id,
            'relationship'         => $request->relationship,
        ]);

        return response()->json([
            'message'    => "{$request->name} added as a dependent.",
            'dependent'  => $dependent->load('dependent.user'),
        ], 201);
    }
}
