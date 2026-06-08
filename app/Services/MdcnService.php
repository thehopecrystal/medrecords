<?php

namespace App\Services;

/**
 * Mock MDCN (Medical and Dental Council of Nigeria) verification service.
 * In production, integrate with MDCN portal API.
 */
class MdcnService
{
    public function verify(string $folioNumber): array
    {
        // Simulate verification — in production call MDCN portal
        if (empty($folioNumber)) {
            return ['verified' => false, 'status' => 'no_folio', 'message' => 'No MDCN folio number provided.'];
        }

        // Simulate: folios ending in odd digit are verified, even are pending
        $last = (int) substr(preg_replace('/\D/', '', $folioNumber), -1);

        if ($last % 2 === 0) {
            return [
                'verified'   => false,
                'status'     => 'pending',
                'message'    => 'Verification pending — awaiting MDCN portal response.',
            ];
        }

        return [
            'verified'    => true,
            'status'      => 'full_registration',
            'message'     => 'Full registration confirmed via MDCN portal.',
            'name'        => 'Confirmed by MDCN',
            'verified_at' => now()->toDateTimeString(),
        ];
    }
}
