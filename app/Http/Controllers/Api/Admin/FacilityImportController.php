<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FacilityImportController extends Controller
{
    /** CSV format spec for API clients. */
    public function create(): JsonResponse
    {
        abort_unless(auth()->user()->role === 'super_admin', 403);

        return response()->json([
            'required_columns' => ['name'],
            'optional_columns' => ['type', 'state', 'city', 'address', 'phone', 'email'],
            'allowed_types'    => ['hospital', 'clinic', 'pharmacy', 'lab', 'dental', 'optical', 'other'],
            'file'             => 'multipart field `csv_file`: csv/txt, max 2048 KB',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->role === 'super_admin', 403);

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $headers  = array_map('trim', fgetcsv($handle));
        $required = ['name'];
        $missing  = array_diff($required, $headers);

        if ($missing) {
            fclose($handle);
            return response()->json(['message' => 'CSV missing required columns: ' . implode(', ', $missing)], 422);
        }

        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) { $skipped++; continue; }

            $data = array_combine($headers, array_map('trim', $row));

            if (empty($data['name'])) { $skipped++; continue; }

            $allowedTypes = ['hospital', 'clinic', 'pharmacy', 'lab', 'dental', 'optical', 'other'];
            $type  = in_array($data['type'] ?? '', $allowedTypes, true) ? $data['type'] : 'hospital';
            $email = isset($data['email']) && filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)
                     ? trim($data['email']) : null;

            Facility::firstOrCreate(
                ['name' => $data['name']],
                [
                    'type'    => $type,
                    'state'   => $data['state']   ?? null,
                    'city'    => $data['city']    ?? null,
                    'address' => $data['address'] ?? null,
                    'phone'   => $data['phone']   ?? null,
                    'email'   => $email,
                ]
            ) ? $imported++ : $skipped++;
        }

        fclose($handle);

        return response()->json([
            'message'  => "Import complete: {$imported} facilities added, {$skipped} skipped (duplicates or empty).",
            'imported' => $imported,
            'skipped'  => $skipped,
        ]);
    }
}
