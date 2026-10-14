<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApiPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_sends_a_reset_link_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create(['password' => Hash::make('old-secret-1')]);

        $this->postJson('/api/auth/forgot-password', ['email' => $user->email])
            ->assertOk()
            ->assertJsonStructure(['message']);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_forgot_password_for_unknown_email_is_not_revealed(): void
    {
        Notification::fake();

        $this->postJson('/api/auth/forgot-password', ['email' => 'nobody@example.com'])
            ->assertStatus(422);

        Notification::assertNothingSent();
    }

    public function test_reset_password_changes_the_password_and_allows_login(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-secret-1')]);

        $token = 
            \Illuminate\Support\Facades\Password::broker('users')->createToken($user);

        $this->postJson('/api/auth/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'new-secret-99',
            'password_confirmation' => 'new-secret-99',
        ])->assertOk()->assertJson(['message' => 'Password reset successfully. Please sign in.']);

        // The new password works, the old one does not.
        $this->postJson('/api/auth/login', [
            'login'    => $user->email,
            'password' => 'new-secret-99',
        ])->assertOk()->assertJsonStructure(['access_token']);

        $this->postJson('/api/auth/login', [
            'login'    => $user->email,
            'password' => 'old-secret-1',
        ])->assertStatus(401);
    }

    public function test_reset_password_rejects_an_invalid_token(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-secret-1')]);

        $this->postJson('/api/auth/reset-password', [
            'token'                 => 'bogus-token',
            'email'                 => $user->email,
            'password'              => 'new-secret-99',
            'password_confirmation' => 'new-secret-99',
        ])->assertStatus(422);

        $this->assertTrue(Hash::check('old-secret-1', $user->fresh()->password));
    }

    public function test_admin_accounts_are_reset_through_the_admins_broker(): void
    {
        Notification::fake();

        $admin = Admin::create([
            'name'     => 'Boss',
            'email'    => 'boss@example.com',
            'phone'    => '+2348012300099',
            'password' => Hash::make('old-secret-1'),
        ]);
        $admin->role = 'super_admin';
        $admin->is_active = true;
        $admin->save();

        // The broker is picked by email lookup — admins get their own token table.
        $this->postJson('/api/auth/forgot-password', ['email' => $admin->email])
            ->assertOk();

        Notification::assertSentTo($admin, ResetPassword::class);

        $token = \Illuminate\Support\Facades\Password::broker('admins')->createToken($admin);

        $this->postJson('/api/auth/reset-password', [
            'token'                 => $token,
            'email'                 => $admin->email,
            'password'              => 'new-secret-99',
            'password_confirmation' => 'new-secret-99',
        ])->assertOk();

        // Admins log in through the same API login (admin guard resolution).
        $this->postJson('/api/auth/login', [
            'login'    => $admin->email,
            'password' => 'new-secret-99',
        ])->assertOk()->assertJsonPath('role', 'super_admin');
    }

    public function test_forgot_password_is_throttled(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 5) as $i) {
            $this->postJson('/api/auth/forgot-password', ['email' => $user->email]);
        }

        $this->postJson('/api/auth/forgot-password', ['email' => $user->email])
            ->assertStatus(429);
    }
}
