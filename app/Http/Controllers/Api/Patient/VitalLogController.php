<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Models\VitalLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VitalLogController extends Controller
{
    public function index(): JsonResponse
    {
        $patient = $this->currentPatient();

        $logs = VitalLog::where('patient_id', $patient->id)
            ->orderByDesc('logged_at')->paginate(20);

        // Last 30 entries for charts (oldest first)
        $chartData = VitalLog::where('patient_id', $patient->id)
            ->orderByDesc('logged_at')->limit(30)->get()->reverse()->values();

        $latest = $logs->first();

        return response()->json(['logs' => $logs, 'chart_data' => $chartData, 'latest' => $latest]);
    }

    public function store(Request $request): JsonResponse
    {
        $patient = $this->currentPatient();

        $request->validate([
            'logged_at'         => 'required|date|before_or_equal:now',
            'bp_systolic'       => 'nullable|integer|between:50,300',
            'bp_diastolic'      => 'nullable|integer|between:30,200',
            'heart_rate'        => 'nullable|integer|between:20,300',
            'temperature_c'     => 'nullable|numeric|between:30,45',
            'weight_kg'         => 'nullable|numeric|between:1,500',
            'glucose_mmol'      => 'nullable|numeric|between:1,50',
            'oxygen_saturation' => 'nullable|integer|between:50,100',
            'pain_level'        => 'nullable|integer|between:0,10',
            'notes'             => 'nullable|string|max:500',
        ]);

        $log = VitalLog::create(array_merge(
            $request->only([
                'logged_at', 'bp_systolic', 'bp_diastolic', 'heart_rate',
                'temperature_c', 'weight_kg', 'glucose_mmol',
                'oxygen_saturation', 'pain_level', 'notes',
            ]),
            ['patient_id' => $patient->id]
        ));

        return response()->json(['message' => 'Vitals logged successfully.', 'vital_log' => $log], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $patient = $this->currentPatient();
        VitalLog::where('patient_id', $patient->id)->where('id', $id)->delete();
        return response()->json(['message' => 'Entry deleted.']);
    }
}
