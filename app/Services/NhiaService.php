<?php

namespace App\Services;

/**
 * Mock NHIA eligibility service.
 * In production, call the NHIA API: https://api.nhia.gov.ng/eligibility
 */
class NhiaService
{
    public function checkEligibility(string $phone): array
    {
        // Simulate API response based on phone patterns for demo purposes
        $last = (int) substr($phone, -1);

        if ($last <= 2) {
            return ['status' => 'not_enrolled', 'plan' => null, 'coverage' => 0, 'message' => 'Not enrolled in NHIA.'];
        }

        if ($last <= 5) {
            return [
                'status'        => 'enrolled',
                'plan'          => 'NHIA Basic',
                'coverage'      => 60,
                'insurer'       => 'AIICO HMO',
                'member_id'     => 'NHIA' . strtoupper(substr($phone, -6)),
                'copay'         => 40,
                'message'       => 'Eligible — 60% covered by NHIA Basic.',
                'annual_limit'  => 150000,
                'balance'       => 112500,
            ];
        }

        return [
            'status'        => 'enrolled',
            'plan'          => 'NHIA Comprehensive',
            'coverage'      => 90,
            'insurer'       => 'Hygeia HMO',
            'member_id'     => 'NHIA' . strtoupper(substr($phone, -6)),
            'copay'         => 10,
            'message'       => 'Eligible — 90% covered by NHIA Comprehensive.',
            'annual_limit'  => 500000,
            'balance'       => 387000,
        ];
    }
}
