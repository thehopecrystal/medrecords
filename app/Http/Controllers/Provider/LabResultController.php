<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LabResult;
use App\Models\Patient;
use Illuminate\Http\Request;

class LabResultController extends Controller
{
    public function create(int $patientId)
    {
        $patient = Patient::with('user')->findOrFail($patientId);
        return view('provider.lab-result-new', compact('patient'));
    }

    public function store(Request $request, int $patientId)
    {
        $request->validate([
            'test_name'    => 'required|string|max:200',
            'result'       => 'required|string|max:100',
            'collected_at' => 'required|date',
        ]);

        $patient = Patient::findOrFail($patientId);

        $lab = LabResult::create([
            'patient_id'      => $patient->id,
            'encounter_id'    => $request->encounter_id ?: null,
            'ordered_by'      => auth()->id(),
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

        return redirect()->route('provider.patient', [$patient->id, 'tab' => 'labs'])
            ->with('success', "Lab result for {$request->test_name} uploaded.");
    }
}
