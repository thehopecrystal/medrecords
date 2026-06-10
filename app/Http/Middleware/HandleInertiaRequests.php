<?php

namespace App\Http\Middleware;

use App\Models\BreakGlassEvent;
use App\Models\Consent;
use App\Models\Notification;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        SiteSetting::loadAll();
        $user     = $request->user();
        $role     = $user?->role ?? 'guest';
        $patient  = ($role === 'patient') ? $user?->patient : null;

        $unreadCount = $user
            ? Notification::where('user_id', $user->id)->whereNull('read_at')->count()
            : 0;

        $pendingConsents = $patient
            ? Consent::where('patient_id', $patient->id)->where('status', 'pending')->exists()
            : false;

        $activeBreakGlass = in_array($role, ['facility_admin', 'super_admin'])
            ? BreakGlassEvent::where('status', 'active')->count()
            : 0;

        return array_merge(parent::share($request), [
            'auth' => [
                'user'   => $user ? [
                    'id'      => $user->id,
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'role'    => $role,
                    'avatar'  => $user->avatar,
                    'patient' => $patient ? [
                        'id'     => $patient->id,
                        'med_id' => $patient->med_id,
                    ] : null,
                ] : null,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
            ],
            'site' => [
                'name'    => SiteSetting::get('site_name', 'MedRecords'),
                'logo'    => SiteSetting::get('site_logo_url', ''),
                'favicon' => SiteSetting::get('site_favicon_url', ''),
            ],
            'ui' => [
                'unreadCount'      => $unreadCount,
                'pendingConsents'  => $pendingConsents,
                'activeBreakGlass' => $activeBreakGlass,
            ],
        ]);
    }
}
