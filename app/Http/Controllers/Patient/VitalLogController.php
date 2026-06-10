<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\VitalLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VitalLogController extends Controller
{
    public function index()
    {
        $patient = $this->currentPatient();

        $logs = VitalLog::where('patient_id', $patient->id)
            ->orderByDesc('logged_at')->paginate(20);

        // Last 30 entries for charts (oldest first)
        $chartData = VitalLog::where('patient_id', $patient->id)
            ->orderByDesc('logged_at')->limit(30)->get()->reverse()->values();

        $latest = $logs->first();

        // Serialize chart data for JS
        $chartData = $chartData->map(fn($l) => [
            'label' => $l->logged_at->format('d M H:i'),
            'sys'   => $l->bp_systolic,
            'dia'   => $l->bp_diastolic,
        ])->values();

        return Inertia::render('Patient/Vitals', compact('patient', 'logs', 'chartData', 'latest'));
    }

    public function store(Request $request)
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

        VitalLog::create(array_merge(
            $request->only([
                'logged_at', 'bp_systolic', 'bp_diastolic', 'heart_rate',
                'temperature_c', 'weight_kg', 'glucose_mmol',
                'oxygen_saturation', 'pain_level', 'notes',
            ]),
            ['patient_id' => $patient->id]
        ));

        return back()->with('success', 'Vitals logged successfully.');
    }

    public function destroy(int $id)
    {
        $patient = $this->currentPatient();
        VitalLog::where('patient_id', $patient->id)->where('id', $id)->delete();
        return back()->with('success', 'Entry deleted.');
    }
}
