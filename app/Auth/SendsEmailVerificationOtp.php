<?php

namespace App\Auth;

use App\Mail\EmailVerificationOtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * OTP-based email verification, shared by User and Admin models.
 *
 * Replaces Laravel's default signed-link verification (`VerifyEmail` mailable).
 * The user-facing transport is now a 6-digit code emailed at registration and
 * resend. Behaviour mirrors the existing 2FA login OTP:
 *
 *  - 6-digit numeric code, generated with random_int(100000, 999999)
 *  - bcrypt hash stored in email_verification_code (never plaintext)
 *  - 10-minute validity window stored in email_verification_expires_at
 *  - 5 wrong-code attempts invalidates the code and forces a resend
 *  - on success: email_verified_at = now(), code fields cleared
 *
 * `MustVerifyEmail::hasVerifiedEmail()` continues to read `email_verified_at`,
 * so no upstream middleware/route changes are required.
 */
trait SendsEmailVerificationOtp
{
    /**
     * Generate a fresh 6-digit OTP, persist its hash + expiry, and email it.
     * Always succeeds — mail failures are logged and do not bubble up.
     */
    public function sendEmailVerificationOtpNotification(): void
    {
        if (!$this->email) {
            return;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->forceFill([
            'email_verification_code'       => Hash::make($code),
            'email_verification_expires_at' => now()->addMinutes(10),
            'email_verification_attempts'   => 0,
        ])->save();

        try {
            Mail::to($this->email)->send(new EmailVerificationOtpMail($code, $this->name ?? ''));
        } catch (\Throwable $e) {
            Log::warning('Email verification OTP send failed for ' . class_basename($this) . " {$this->id}: " . $e->getMessage());
        }
    }

    /**
     * Validate the submitted code. Returns one of:
     *   'verified'    — first successful verification
     *   'already'     — already verified (no-op)
     *   'expired'     — code present but past expiry
     *   'no_code'     — no code on file (user must click resend)
     *   'too_many'    — 5 wrong attempts already; must resend
     *   'invalid'     — wrong code (attempt counter incremented)
     */
    public function consumeEmailVerificationOtp(string $submitted): string
    {
        if ($this->hasVerifiedEmail()) {
            return 'already';
        }

        if (empty($this->email_verification_code) || empty($this->email_verification_expires_at)) {
            return 'no_code';
        }

        if ((int) $this->email_verification_attempts >= 5) {
            return 'too_many';
        }

        if (now()->isAfter($this->email_verification_expires_at)) {
            return 'expired';
        }

        if (!Hash::check($submitted, $this->email_verification_code)) {
            $this->email_verification_attempts = ((int) $this->email_verification_attempts) + 1;
            $this->save();
            return 'invalid';
        }

        // Success — mark verified, clear the code.
        $this->forceFill([
            'email_verified_at'             => now(),
            'email_verification_code'       => null,
            'email_verification_expires_at' => null,
            'email_verification_attempts'   => 0,
        ])->save();

        return 'verified';
    }

    /**
     * Convenience accessor used by the registration flows to decide whether
     * to auto-verify (e.g. admins created in the demo seeder).
     */
    public function isEmailVerified(): bool
    {
        return $this->hasVerifiedEmail();
    }
}
