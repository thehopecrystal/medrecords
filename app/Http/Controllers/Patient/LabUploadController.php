<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\LabResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LabUploadController extends Controller
{
    public function create()
    {
        $patient = $this->currentPatient();
        return Inertia::render('Patient/LabUpload', compact('patient'));
    }

    public function store(Request $request)
    {
        $patient = $this->currentPatient();

        $request->validate([
            'test_name'   => 'required|string|max:150',
            'result'      => 'required|string|max:500',
            'collected_at'=> 'required|date|before_or_equal:today',
            'flag'        => 'nullable|in:normal,high,low,critical',
            'notes'       => 'nullable|string|max:500',
            // Store privately — never in public disk.
            'file'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            // 'local' disk → storage/app/  (not web-accessible)
            $filePath = $request->file('file')->store("lab-results/{$patient->id}", 'local');
        }

        LabResult::create([
            'patient_id'       => $patient->id,
            'ordered_by'       => $this->currentUser()->id,
            'test_name'        => $request->test_name,
            'result'           => $request->result,
            'flag'             => $request->flag ?? 'normal',
            'collected_at'     => $request->collected_at,
            'notes'            => $request->notes,
            'file_path'        => $filePath,
            'patient_uploaded' => true,
        ]);

        return redirect()->route('patient.record', 'labs')
            ->with('success', 'Lab result uploaded successfully.');
    }

    public function download(int $id)
    {
        $patient   = $this->currentPatient();
        $labResult = LabResult::where('patient_id', $patient->id)->findOrFail($id);

        if (!$labResult->file_path || !Storage::disk('local')->exists($labResult->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download($labResult->file_path);
    }
}
