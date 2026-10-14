<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    private function patientUser(array $attrs = []): User
    {
        $user = User::factory()->create(array_merge([
            'role'     => 'patient',
            'phone'    => '+2348012340001',
            'password' => Hash::make('secret1234'),
        ], $attrs));

        $patient = new Patient(['med_id' => 'MR-TEST0001-ABCDEF']);
        $patient->user_id = $user->id;
        $patient->save();

        return $user;
    }

    private function adminUser(string $role, array $attrs = []): Admin
    {
        $admin = Admin::create(array_merge([
            'name'              => fake()->name(),
            'email_verified_at' => now(),
            'email'    => fake()->unique()->safeEmail(),
            'phone'    => '+23480123400' . random_int(10, 99),
            'password' => Hash::make('secret1234'),
        ], $attrs));
        $admin->role = $role;
        $admin->is_active = true;
        $admin->save();
        $admin->forceFill(['email_verified_at' => now()])->save();

        return $admin;
    }


    /**
     * tymon's JWTGuard caches the resolved user on the singleton guard, which
     * leaks across in-process test requests. Drop cached guards so the next
     * request re-resolves the guard and re-parses the presented token.
     */
    private function resetGuards(): void
    {
        // tymon's JWT singleton keeps the last parsed token on the instance;
        // drop it along with the cached guards so requests are isolated.
        \Illuminate\Support\Facades\Facade::clearResolvedInstances();
        $this->app->forgetInstance('tymon.jwt');
        $this->app->forgetInstance('tymon.jwt.auth');
        $this->app['auth']->forgetGuards();
    }

    /** Mint a token directly from a model, bypassing guard state entirely. */
    private function tokenFor(User|Admin $model): string
    {
        return JWTAuth::fromUser($model);
    }

    public function test_login_returns_a_jwt_and_me_resolves_the_user(): void
    {
        $user = $this->patientUser();

        $response = $this->postJson('/api/auth/login', [
            'login'    => $user->email,
            'password' => 'secret1234',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in', 'role', 'user'])
            ->assertJsonPath('role', 'patient');

        $this->getJson('/api/auth/me', [
            'Authorization' => 'Bearer ' . $response->json('access_token'),
        ])->assertOk()->assertJsonPath('role', 'patient');
    }

    public function test_invalid_credentials_return_401(): void
    {
        $user = $this->patientUser();

        $this->postJson('/api/auth/login', [
            'login'    => $user->email,
            'password' => 'wrong-password',
        ])->assertStatus(401)->assertJson(['message' => 'Invalid credentials. Please check your email/phone and password.']);
    }

    public function test_protected_endpoints_require_a_token(): void
    {
        $this->getJson('/api/patient/dashboard')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_token_can_refresh_and_logout_invalidates_it(): void
    {
        $user = $this->patientUser();
        $token = $this->tokenFor($user);

        $refreshed = $this->postJson('/api/auth/refresh', [], [
            'Authorization' => "Bearer {$token}",
        ])->assertOk()->json('access_token');

        $this->resetGuards();
        $this->getJson('/api/auth/me', ['Authorization' => "Bearer {$refreshed}"])->assertOk();

        $this->resetGuards();
        $this->postJson('/api/auth/logout', [], ['Authorization' => "Bearer {$refreshed}"])->assertOk();

        $this->resetGuards();
        $this->getJson('/api/auth/me', ['Authorization' => "Bearer {$refreshed}"])->assertStatus(401);
    }

    public function test_two_factor_login_issues_a_pending_token_then_a_full_token(): void
    {
        $user = $this->patientUser(['two_factor_enabled' => true]);

        $login = $this->postJson('/api/auth/login', [
            'login'    => $user->email,
            'password' => 'secret1234',
        ])->assertOk()->assertJsonPath('requires_2fa', true);

        $pending = $login->json('access_token');

        // Pending tokens are blocked on every other endpoint...
        $this->resetGuards();
        $this->getJson('/api/patient/dashboard', ['Authorization' => "Bearer {$pending}"])
            ->assertStatus(403)
            ->assertJsonPath('code', 'two_factor_pending');

        // ...and a wrong OTP is rejected.
        $this->resetGuards();
        $this->postJson('/api/auth/2fa/verify', ['otp' => '000000'], ['Authorization' => "Bearer {$pending}"])
            ->assertStatus(422);

        // A correct OTP exchanges the pending token for a full one.
        $user->forceFill([
            'otp_code'       => Hash::make('123456'),
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();

        $pendingToken = JWTAuth::claims(['2fa_pending' => true])->fromUser($user->fresh());

        $this->resetGuards();
        $verified = $this->postJson('/api/auth/2fa/verify', ['otp' => '123456'], ['Authorization' => "Bearer {$pendingToken}"])
            ->assertOk()
            ->assertJsonPath('message', 'Two-factor verification passed.');

        $this->getJson('/api/auth/me', ['Authorization' => 'Bearer ' . $verified->json('access_token')])->assertOk();
    }

    public function test_registration_returns_a_token_and_creates_the_profile(): void
    {
        $this->postJson('/api/auth/register', [
            'terms'    => true,
            'name'     => 'Ada Obi',
            'email'    => 'ada@example.com',
            'phone'    => '+2348099998888',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])->assertCreated()
            ->assertJsonPath('role', 'patient')
            ->assertJsonPath('email_verified', false);

        $user = User::where('email', 'ada@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('patient', $user->role);
        $this->assertNotNull($user->patient);
    }

    public function test_roles_are_enforced_across_api_scopes(): void
    {
        $provider = User::factory()->create([
            'role'               => 'provider',
            'phone'              => '+2348012340002',
            'password'           => Hash::make('secret1234'),
            'two_factor_enabled' => true,
        ]);
        Provider::create(['user_id' => $provider->id, 'specialty' => 'General']);
        $token = $this->tokenFor($provider);

        $this->resetGuards();
        $this->getJson('/api/patient/dashboard', ['Authorization' => "Bearer {$token}"])->assertStatus(403);
    }

    public function test_unverified_provider_is_blocked_from_patient_data(): void
    {
        $provider = User::factory()->create([
            'role'               => 'provider',
            'phone'              => '+2348012340003',
            'password'           => Hash::make('secret1234'),
            'two_factor_enabled' => true,
        ]);
        Provider::create(['user_id' => $provider->id, 'specialty' => 'General']);
        $patient = $this->patientUser();

        $token = $this->tokenFor($provider);

        $this->resetGuards();
        $this->getJson("/api/provider/patients/{$patient->patient->id}", ['Authorization' => "Bearer {$token}"])
            ->assertStatus(403)
            ->assertJsonPath('code', 'provider_pending_verification');
    }

    public function test_admin_login_uses_the_admin_guard_and_super_admin_sees_users(): void
    {
        $super = $this->adminUser('super_admin', ['two_factor_enabled' => true]);

        $login = $this->postJson('/api/auth/login', [
            'login'    => $super->email,
            'password' => 'secret1234',
        ])->assertOk()->assertJsonPath('role', 'super_admin');

        $token = $login->json('access_token');

        $this->resetGuards();
        $this->getJson('/api/admin/users', ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonStructure(['users', 'role_counts']);
    }

    public function test_facility_admin_cannot_access_super_admin_endpoints(): void
    {
        $admin = $this->adminUser('facility_admin', ['two_factor_enabled' => true]);
        $token = $this->tokenFor($admin);

        $this->resetGuards();
        $this->getJson('/api/admin/users', ['Authorization' => "Bearer {$token}"])
            ->assertStatus(403)
            ->assertJson(['message' => 'Super admin access required.']);
    }

    public function test_patient_dashboard_returns_json_data(): void
    {
        $user  = $this->patientUser();
        $token = $this->tokenFor($user);

        $this->resetGuards();
        $this->getJson('/api/patient/dashboard', ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonStructure([
                'patient',
                'recent_activity',
                'upcoming_appointments',
                'latest_vitals',
                'active_consent_count',
            ]);
    }
}
