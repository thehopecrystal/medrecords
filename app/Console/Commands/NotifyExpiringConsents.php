<?php

namespace App\Console\Commands;

use App\Models\Consent;
use App\Models\Notification;
use Illuminate\Console\Command;

class NotifyExpiringConsents extends Command
{
    protected $signature   = 'consents:notify-expiring';
    protected $description = 'Send in-app notifications for consents expiring within 24 hours';

    public function handle(): void
    {
        $expiring = Consent::with(['patient.user', 'grantee'])
            ->where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addHours(24)])
            ->get();

        foreach ($expiring as $consent) {
            $patientUserId = $consent->patient?->user?->id;
            if (!$patientUserId) continue;

            $providerName = $consent->grantee?->name ?? 'a provider';
            $expiresIn    = $consent->expires_at->diffForHumans();

            Notification::send(
                $patientUserId,
                'consent_expiry',
                'Consent Expiring Soon',
                "Your consent granted to {$providerName} expires {$expiresIn}. Visit Consent Management to renew or let it lapse.",
                ['consent_id' => $consent->id]
            );
        }

        $this->info("Notified patients for {$expiring->count()} expiring consent(s).");
    }
}
