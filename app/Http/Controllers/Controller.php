<?php

namespace App\Http\Controllers;

use App\Models\BreakGlassEvent;
use App\Models\Consent;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\User;

abstract class Controller
{
    protected function currentUser(): User
    {
        /** @var User $user */
        $user = auth()->user();
        return $user;
    }

    protected function currentPatient(): Patient
    {
        $patient = $this->currentUser()->patient;
        abort_if($patient === null, 403, 'No patient profile found for this account.');
        return $patient;
    }

    protected function currentProvider(): Provider
    {
        $provider = $this->currentUser()->provider;
        abort_if($provider === null, 403, 'No provider profile found for this account.');
        return $provider;
    }

    /**
     * Abort with 403 unless the authenticated provider holds an active consent
     * grant OR an unexpired break-glass event for the given patient.
     * Used by all provider controllers that write PHI.
     */
    protected function requireConsent(int $patientId): void
    {
        $hasConsent = Consent::where('patient_id', $patientId)
            ->where('grantee_id', auth()->id())
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->exists();

        $hasBreakGlass = BreakGlassEvent::where('patient_id', $patientId)
            ->where('provider_id', auth()->id())
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->exists();

        abort_unless(
            $hasConsent || $hasBreakGlass,
            403,
            'You do not have consent to access this patient\'s record. Request consent first or use break-glass for emergencies.'
        );
    }
}
