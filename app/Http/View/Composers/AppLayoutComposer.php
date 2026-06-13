<?php

namespace App\Http\View\Composers;

use App\Models\BreakGlassEvent;
use App\Models\Consent;
use App\Models\Notification;
use App\Models\SiteSetting;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AppLayoutComposer
{
    public function compose(View $view): void
    {
        SiteSetting::loadAll();

        $authUser  = auth()->user();
        $userRole  = $authUser?->role ?? 'guest';
        $userName  = $authUser?->name ?? '';
        $nameParts = explode(' ', $userName);
        $initials  = strtoupper(
            substr($nameParts[0] ?? '', 0, 1) . substr($nameParts[1] ?? '', 0, 1)
        );

        $unreadCount = $authUser
            ? Notification::where('user_id', $authUser->id)->whereNull('read_at')->count()
            : 0;

        $patientId = ($userRole === 'patient') ? $authUser?->patient?->id : null;

        $pendingConsents = $patientId
            ? Consent::where('patient_id', $patientId)->where('status', 'pending')->exists()
            : false;

        $activeBreakGlass = in_array($userRole, ['facility_admin', 'super_admin'])
            ? BreakGlassEvent::where('status', 'active')->count()
            : 0;

        $view->with([
            'siteName'         => SiteSetting::get('site_name', 'MedRecords'),
            'logoUrl'          => SiteSetting::get('site_logo_url', ''),
            'faviconUrl'       => SiteSetting::get('site_favicon_url', ''),
            'authUser'         => $authUser,
            'userRole'         => $userRole,
            'userName'         => $userName,
            'initials'         => $initials,
            'unreadCount'      => $unreadCount,
            'pendingConsents'  => $pendingConsents,
            'activeBreakGlass' => $activeBreakGlass,
        ]);
    }
}
