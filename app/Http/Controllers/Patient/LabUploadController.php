<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\LabResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LabUploadController extends Controller
{
    public function create()
    {
        $patient = $this->currentPatient();
        return view('patient.lab-upload', compact('patient'));
    }

    public function store(Request $request)
    {
        $patient = $this->currentPatient();

        $request->validate([
            'test_name'    => 'required|string|max:150',
            'result'       => 'required|string|max:500',
            'collected_at' => 'required|date|before_or_equal:today',
            'flag'         => 'nullable|in:normal,high,low,critical',
            'notes'        => 'nullable|string|max:500',
            // Stored privately — never on public disk.
            'file'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $ext      = $request->file('file')->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . strtolower($ext);
            $filePath = $request->file('file')->storeAs(
                "lab-results/{$patient->id}",
                $filename,
                'local'
            );
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

        if (!$labResult->file_path) {
            abort(404, 'No file attached to this result.');
        }

        // Prevent path traversal — stored path must be inside this patient's directory.
        $expectedPrefix = "lab-results/{$patient->id}/";
        if (Str::startsWith($labResult->file_path, '/') || !Str::startsWith($labResult->file_path, $expectedPrefix)) {
            abort(403, 'Unauthorized file access.');
        }

        if (!Storage::disk('local')->exists($labResult->file_path)) {
            abort(404, 'File not found.');
        }

        $absolutePath = Storage::disk('local')->path($labResult->file_path);
        return response()->download($absolutePath);
    }
}
