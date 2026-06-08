<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BreakGlassEvent;
use App\Models\Facility;
use App\Models\Patient;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'patients'    => Patient::count(),
            'providers'   => User::where('role', 'provider')->count(),
            'facilities'  => Facility::count(),
            'break_glass' => BreakGlassEvent::where('status', 'active')->count(),
        ];

        $recentLogs  = AuditLog::with(['patient.user', 'actor'])->latest()->take(20)->get();
        $breakGlass  = BreakGlassEvent::with(['patient.user', 'provider'])->where('status', 'active')->latest()->take(10)->get();
        $facilities  = Facility::withCount('providers')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentLogs', 'breakGlass', 'facilities'));
    }

    public function facilities()
    {
        $facilities = Facility::withCount('providers')->orderBy('name')->paginate(30);
        return view('admin.facilities', compact('facilities'));
    }

    public function breakGlass()
    {
        $events = BreakGlassEvent::with(['patient.user', 'provider'])
            ->latest()
            ->paginate(30);
        return view('admin.break-glass', compact('events'));
    }
}
