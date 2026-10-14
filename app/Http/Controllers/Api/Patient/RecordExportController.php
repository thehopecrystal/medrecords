<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;

class RecordExportController extends Controller
{
    /** Full health record as a PDF download (same document as the web flow). */
    public function export()
    {
        $patient = $this->currentPatient();

        $patient->load([
            'user',
            'allergies',
            'conditions',
            'encounters.facility',
            'encounters.provider',
            'prescriptions.prescribedBy',
            'vaccinations',
            'labResults',
            'procedures',
            'hospitalizations',
            'referrals',
        ]);

        AuditLog::record(
            $patient->id,
            (int) auth()->id(),
            'record_exported',
            'Patient exported their full health record as PDF',
            'Patient',
            $patient->id
        );

        $pdf = Pdf::loadView('patient.record-export-pdf', compact('patient'))
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false)
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = 'MedRecord-' . $patient->med_id . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename)->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
        ]);
    }
}
