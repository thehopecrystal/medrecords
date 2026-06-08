<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user    = auth()->user();
        $patient = $user->patient;

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
}
