<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date_format:Y-m-d',
            'to'   => 'nullable|date_format:Y-m-d',
        ]);

        $patient = $this->currentPatient();

        $query = $patient->auditLogs()->with('actor');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('actor')) {
            $query->whereHas('actor', fn($q) => $q->where('name', 'like', '%' . $request->actor . '%'));
        }

        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from . ' 00:00:00');
        }

        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to . ' 23:59:59');
        }

        $logs = $query->latest()->paginate(30)->withQueryString();

        $actions = $patient->auditLogs()->distinct()->pluck('action')->sort()->values();

        return view('patient.audit-log', compact('patient', 'logs', 'actions'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date_format:Y-m-d',
            'to'   => 'nullable|date_format:Y-m-d',
        ]);

        $patient = $this->currentPatient();

        $query = $patient->auditLogs()->with('actor');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('actor')) {
            $query->whereHas('actor', fn($q) => $q->where('name', 'like', '%' . $request->actor . '%'));
        }
        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from . ' 00:00:00');
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to . ' 23:59:59');
        }

        $logs = $query->latest()->limit(10000)->get();

        $filename = 'audit-log-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, ['Date/Time', 'Action', 'Actor', 'Details', 'IP Address']);
            foreach ($logs as $log) {
                fputcsv($fh, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->action,
                    $log->actor?->name ?? 'System',
                    $log->details ?? '',
                    $log->ip_address ?? '',
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }
}
