<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Models\LabResult;
use App\Rules\SafeFileType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LabUploadController extends Controller
{
    /** Upload-form metadata for API clients. */
    public function create(): JsonResponse
    {
        return response()->json([
            'fields' => [
                'test_name'    => 'required|string|max:150',
                'result'       => 'required|string|max:500',
                'collected_at' => 'required|date|before_or_equal:today',
                'flag'         => 'nullable|in:normal,high,low,critical',
                'notes'        => 'nullable|string|max:500',
                'file'         => 'optional file: pdf,jpg,jpeg,png (max 5120 KB) — stored privately',
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $patient = $this->currentPatient();

        $request->validate([
            'test_name'    => 'required|string|max:150',
            'result'       => 'required|string|max:500',
            'collected_at' => 'required|date|before_or_equal:today',
            'flag'         => 'nullable|in:normal,high,low,critical',
            'notes'        => 'nullable|string|max:500',
            // Stored privately - never on public disk.
            'file'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120',
                              new SafeFileType(['application/pdf', 'image/jpeg', 'image/png'])],
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $ext = match($request->file('file')->getMimeType()) {
                'application/pdf' => 'pdf',
                'image/jpeg'      => 'jpg',
                'image/png'       => 'png',
                default           => 'bin',
            };
            $filename = Str::uuid() . '.' . $ext;
            $filePath = $request->file('file')->storeAs(
                "lab-results/{$patient->id}",
                $filename,
                'local'
            );
        }

        $lab = LabResult::create([
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

        return response()->json(['message' => 'Lab result uploaded successfully.', 'lab_result' => $lab], 201);
    }

    public function download(int $id)
    {
        $patient   = $this->currentPatient();
        $labResult = LabResult::where('patient_id', $patient->id)->findOrFail($id);

        if (!$labResult->file_path) {
            abort(404, 'No file attached to this result.');
        }

        // Prevent path traversal - stored path must be inside this patient's directory.
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
