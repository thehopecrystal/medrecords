<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;

class RecordExportController extends Controller
{
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

        return $pdf->download($filename);
    }
}
