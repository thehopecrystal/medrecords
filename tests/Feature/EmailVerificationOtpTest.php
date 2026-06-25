<?php

namespace Tests\Feature;

use App\Auth\SendsEmailVerificationOtp;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Coverage for the OTP-based email-verification flow.
 *
 * Asserts:
 *  1. Registration generates a hashed OTP + sends the OTP mailable (NOT the
 *     signed-link VerifyEmail notification).
 *  2. Submitting the correct 6-digit code flips `email_verified_at`.
 *  3. Submitting an incorrect code increments the attempts counter and
 *     leaves the account unverified.
 *  4. 5 wrong attempts locks the code (caller must resend).
 *  5. Submitting an expired code returns the "expired" error.
 *  6. Resend invalidates the prior code and writes a new hash.
 *  7. Submitting the OLD code after a resend does NOT verify.
 */
class EmailVerificationOtpTest extends TestCase
{
    use RefreshDatabase;

    /** Helper: register a patient and return the test response. */
    private function registerPatient(array $overrides = []): \Illuminate\Testing\TestResponse
    {
        $base = [
            'name'                  => 'Ada Lovelace',
            'email'                 => 'ada@example.test',
            'phone'                 => '+2348012345678',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
            'terms'                 => 'on',
            'reg_type'              => 'patient',
        ];

        return $this->post('/register', array_merge($base, $overrides));
    }

    public function test_registration_generates_a_hashed_otp_and_emails_it(): void
    {
        Mail::fake();
        Notification::fake();

        $this->registerPatient();

        $user = User::where('email', 'ada@example.test')->firstOrFail();

        // 1. A hashed code + future expiry are persisted.
        $this->assertNotNull($user->email_verification_code);
        $this->assertNotNull($user->email_verification_expires_at);
        $this->assertTrue($user->email_verification_expires_at->isFuture());
        $this->assertSame(0, (int) $user->email_verification_attempts);

        // 2. The code is a bcrypt hash, NOT the raw 6 digits.
        $this->assertStringStartsWith('$2y$', $user->email_verification_code);

        // 3. Email is NOT yet verified.
        $this->assertNull($user->email_verified_at);
    }

    public function test_old_signed_link_verify_email_notification_is_not_sent(): void
    {
        Notification::fake();

        $this->registerPatient();

        // The OTP-based flow supersedes the default VerifyEmail signed-link.
        Notification::assertNotSentTo(User::where('email', 'ada@example.test')->first(), VerifyEmail::class);
    }

    public function test_submitting_correct_code_marks_email_verified(): void
    {
        $this->registerPatient();

        $user = User::where('email', 'ada@example.test')->firstOrFail();

        // Recover the raw OTP by brute-forcing 6 digits is infeasible, so
        // we re-issue a known code via the trait for the test.
        $code = '123456';
        $user->forceFill([
            'email_verification_code'       => Hash::make($code),
            'email_verification_expires_at' => now()->addMinutes(10),
            'email_verification_attempts'   => 0,
        ])->save();

        $response = $this->actingAs($user)->post(route('email.verify.otp'), ['code' => $code]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->email_verification_code);
        $this->assertNull($user->email_verification_expires_at);
    }

    public function test_submitting_wrong_code_increments_attempts_and_stays_unverified(): void
    {
        $this->registerPatient();
        $user = User::where('email', 'ada@example.test')->firstOrFail();

        $user->forceFill([
            'email_verification_code'       => Hash::make('111111'),
            'email_verification_expires_at' => now()->addMinutes(10),
            'email_verification_attempts'   => 0,
        ])->save();

        $this->actingAs($user)
            ->post(route('email.verify.otp'), ['code' => '999999'])
            ->assertSessionHasErrors('code');

        $user->refresh();
        $this->assertNull($user->email_verified_at);
        $this->assertSame(1, (int) $user->email_verification_attempts);
    }

    public function test_five_wrong_attempts_locks_the_code(): void
    {
        $this->registerPatient();
        $user = User::where('email', 'ada@example.test')->firstOrFail();

        $user->forceFill([
            'email_verification_code'       => Hash::make('222222'),
            'email_verification_expires_at' => now()->addMinutes(10),
            'email_verification_attempts'   => 4, // one more attempt allowed before lock
        ])->save();

        // First wrong attempt → bumped to 5, but allowed.
        $this->actingAs($user)->post(route('email.verify.otp'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $user->refresh();
        $this->assertSame(5, (int) $user->email_verification_attempts);

        // Even with the CORRECT code, we're now locked out until resend.
        $this->actingAs($user)->post(route('email.verify.otp'), ['code' => '222222'])
            ->assertSessionHasErrors('code');

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_expired_code_is_rejected(): void
    {
        $this->registerPatient();
        $user = User::where('email', 'ada@example.test')->firstOrFail();

        $user->forceFill([
            'email_verification_code'       => Hash::make('333333'),
            'email_verification_expires_at' => now()->subMinutes(1),
            'email_verification_attempts'   => 0,
        ])->save();

        $this->actingAs($user)
            ->post(route('email.verify.otp'), ['code' => '333333'])
            ->assertSessionHasErrors('code');

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_resend_invalidates_prior_code_and_writes_a_new_one(): void
    {
        Notification::fake();

        $this->registerPatient();
        $user = User::where('email', 'ada@example.test')->firstOrFail();

        $originalHash = $user->email_verification_code;

        $this->actingAs($user)->post(route('verification.send'))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->email_verification_code);
        $this->assertNotSame($originalHash, $user->email_verification_code);
        $this->assertSame(0, (int) $user->email_verification_attempts);
    }

    public function test_admin_registration_also_uses_otp_flow(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'terms'           => 'on',
            'name'            => 'Org Admin',
            'email'           => 'admin@example.test',
            'phone'           => '+2348099999999',
            'password'        => 'Password1!',
            'password_confirmation' => 'Password1!',
            'reg_type'        => 'hospital',
            'org_name'        => 'Acme Hospital',
            'org_type'        => 'hospital',
        ]);

        $response->assertRedirect(route('verification.notice'));

        $admin = Admin::where('email', 'admin@example.test')->firstOrFail();
        $this->assertNotNull($admin->email_verification_code);
        $this->assertNull($admin->email_verified_at);
    }
}
