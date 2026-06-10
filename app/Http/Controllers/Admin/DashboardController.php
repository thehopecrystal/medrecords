<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BreakGlassEvent;
use App\Models\Facility;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'patients'    => Patient::count(),
            'providers'   => User::where('role', 'provider')->count(),
            'facilities'  => Facility::count(),
            'break_glass' => BreakGlassEvent::where('status', 'active')->count(),
            'pharmacists' => User::where('role', 'pharmacist')->count(),
            'total_users' => User::count(),
        ];

        $recentLogs = AuditLog::with(['patient.user', 'actor'])->latest()->take(10)->get();
        $breakGlass = BreakGlassEvent::with(['patient.user', 'provider'])->where('status', 'active')->latest()->take(5)->get();

        // Chart: new registrations per day for the last 30 days
        $regData = DB::table('users')
            ->selectRaw('DATE(created_at) as day, COUNT(*) as cnt')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('cnt', 'day');

        $regLabels = [];
        $regCounts = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $regLabels[] = now()->subDays($i)->format('M d');
            $regCounts[] = $regData[$d] ?? 0;
        }

        // Chart: encounters per week for last 8 weeks
        $encData = DB::table('encounters')
            ->selectRaw('YEARWEEK(created_at, 1) as yw, COUNT(*) as cnt')
            ->where('created_at', '>=', now()->subWeeks(7)->startOfWeek())
            ->groupBy('yw')
            ->orderBy('yw')
            ->pluck('cnt', 'yw');

        $encLabels = [];
        $encCounts = [];
        for ($i = 7; $i >= 0; $i--) {
            $week = now()->subWeeks($i);
            $yw   = $week->format('oW');
            $encLabels[] = 'W' . $week->format('W');
            $encCounts[] = $encData[$yw] ?? 0;
        }

        // Chart: user role distribution
        $roleRows  = User::selectRaw('role, COUNT(*) as cnt')->groupBy('role')->pluck('cnt', 'role');
        $roleLabels = ['Patient', 'Provider', 'Pharmacist', 'Facility Admin', 'Super Admin'];
        $roleValues = [
            $roleRows['patient'] ?? 0,
            $roleRows['provider'] ?? 0,
            $roleRows['pharmacist'] ?? 0,
            $roleRows['facility_admin'] ?? 0,
            $roleRows['super_admin'] ?? 0,
        ];

        // Chart: dispenses per month for last 6 months
        $dispData = DB::table('dispenses')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mo, COUNT(*) as cnt')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('mo')
            ->orderBy('mo')
            ->pluck('cnt', 'mo');

        $dispLabels = [];
        $dispCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $mo = now()->subMonths($i)->format('Y-m');
            $dispLabels[] = now()->subMonths($i)->format('M Y');
            $dispCounts[] = $dispData[$mo] ?? 0;
        }

        $charts = compact('regLabels', 'regCounts', 'encLabels', 'encCounts',
                          'roleLabels', 'roleValues', 'dispLabels', 'dispCounts');

        // Flatten relations so React doesn't need deep nesting
        $recentLogsData = $recentLogs->map(fn ($log) => [
            'id'              => $log->id,
            'action'          => $log->action,
            'actor_name'      => $log->actor?->name ?? 'System',
            'created_at_human'=> $log->created_at->diffForHumans(),
        ]);

        $breakGlassData = $breakGlass->map(fn ($e) => [
            'id'                => $e->id,
            'patient_name'      => $e->patient?->user?->name ?? 'Unknown patient',
            'provider_name'     => $e->provider?->name ?? '—',
            'status'            => $e->status,
            'created_at_human'  => $e->created_at->diffForHumans(),
        ]);

        return Inertia::render('Admin/Dashboard', [
            'stats'      => $stats,
            'recentLogs' => $recentLogsData,
            'breakGlass' => $breakGlassData,
            'charts'     => $charts,
        ]);
    }

    public function facilities()
    {
        $facilities = Facility::withCount('providers')->orderBy('name')->paginate(30);

        return Inertia::render('Admin/Facilities', [
            'facilities' => $facilities,
        ]);
    }

    public function breakGlass()
    {
        $events = BreakGlassEvent::with(['patient.user', 'provider'])
            ->latest()
            ->paginate(30);

        $eventsData = $events->through(fn ($e) => [
            'id'                  => $e->id,
            'patient_name'        => $e->patient?->user?->name ?? 'Unknown patient',
            'provider_name'       => $e->provider?->name ?? '—',
            'status'              => $e->status,
            'reason_code'         => $e->reason_code,
            'justification'       => $e->justification,
            'review_notes'        => $e->review_notes,
            'created_at_formatted'=> $e->created_at->format('d M Y H:i'),
        ]);

        return Inertia::render('Admin/BreakGlass', [
            'events' => $eventsData,
        ]);
    }
}
