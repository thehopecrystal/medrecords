<?php

namespace App\Http\Controllers\Api\Provider;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LabResult;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabResultController extends Controller
{
    /** Lab-order form context. */
    public function create(int $patientId): JsonResponse
    {
        $this->requireConsent($patientId);
        $patient = Patient::with('user')->findOrFail($patientId);

        return response()->json(['patient' => $patient]);
    }

    public function store(Request $request, int $patientId): JsonResponse
    {
        $this->requireConsent($patientId);
        $request->validate([
            'test_name'       => 'required|string|max:200',
            'result'          => 'required|string|max:100',
            'collected_at'    => 'required|date',
            'encounter_id'    => 'nullable|integer|exists:encounters,id',
            'flag'            => 'nullable|in:normal,low,high,critical',
            'loinc_code'      => 'nullable|string|max:20',
            'unit'            => 'nullable|string|max:50',
            'reference_range' => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:2000',
        ]);

        $patient = Patient::findOrFail($patientId);

        // Prevent IDOR: encounter must belong to this patient.
        if ($request->encounter_id) {
            abort_unless(
                $patient->encounters()->where('id', $request->encounter_id)->exists(),
                422,
                'Encounter does not belong to this patient.'
            );
        }

        $lab = LabResult::create([
            'patient_id'      => $patient->id,
            'encounter_id'    => $request->encounter_id ?: null,
            'ordered_by'      => (int) auth()->id(),
            'test_name'       => $request->test_name,
            'loinc_code'      => $request->loinc_code,
            'result'          => $request->result,
            'unit'            => $request->unit,
            'reference_range' => $request->reference_range,
            'flag'            => $request->flag ?? 'normal',
            'collected_at'    => $request->collected_at,
            'notes'           => $request->notes,
        ]);

        AuditLog::record($patient->id, auth()->id(), 'lab_uploaded', "Lab result uploaded: {$request->test_name} = {$request->result}", 'LabResult', $lab->id);

        return response()->json(['message' => "Lab result for {$request->test_name} uploaded.", 'lab_result' => $lab], 201);
    }
}
