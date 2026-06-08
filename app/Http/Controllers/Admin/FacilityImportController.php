<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityImportController extends Controller
{
    public function create()
    {
        abort_unless(auth()->user()->role === 'super_admin', 403);
        return view('admin.facilities-import');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'super_admin', 403);

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $headers = array_map('trim', fgetcsv($handle));
        $required = ['name'];
        $missing  = array_diff($required, $headers);

        if ($missing) {
            fclose($handle);
            return back()->with('error', 'CSV missing required columns: ' . implode(', ', $missing));
        }

        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) { $skipped++; continue; }

            $data = array_combine($headers, array_map('trim', $row));

            if (empty($data['name'])) { $skipped++; continue; }

            Facility::firstOrCreate(
                ['name' => $data['name']],
                [
                    'type'    => $data['type']    ?? 'hospital',
                    'state'   => $data['state']   ?? null,
                    'city'    => $data['city']    ?? null,
                    'address' => $data['address'] ?? null,
                    'phone'   => $data['phone']   ?? null,
                    'email'   => $data['email']   ?? null,
                ]
            ) ? $imported++ : $skipped++;
        }

        fclose($handle);

        return redirect()->route('admin.facilities')
            ->with('success', "Import complete: {$imported} facilities added, {$skipped} skipped (duplicates or empty).");
    }
}
