<?php

namespace Tests\Feature\Users;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Models\AuditLog;
use Modules\Users\Models\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->get('/admin/login')->assertOk()->assertSee('Sign in to the admin panel');
    }

    public function test_active_users_can_sign_in_and_it_is_audited(): void
    {
        $user = User::factory()->role('reviewer')->create(['email' => 'rev@example.com']);

        $this->post('/admin/login', ['email' => 'REV@example.com', 'password' => 'Password-123'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
        $this->assertTrue(AuditLog::query()->where('action', 'auth.login')->where('user_id', $user->id)->exists());
    }

    public function test_wrong_password_fails_and_is_audited(): void
    {
        User::factory()->create(['email' => 'rev@example.com']);

        $this->from('/admin/login')->post('/admin/login', ['email' => 'rev@example.com', 'password' => 'nope'])
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertTrue(AuditLog::query()->where('action', 'auth.login_failed')->exists());
    }

    public function test_deactivated_users_cannot_sign_in_or_keep_a_session(): void
    {
        $user = User::factory()->inactive()->create(['email' => 'old@example.com']);

        $this->post('/admin/login', ['email' => 'old@example.com', 'password' => 'Password-123'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->actingAs($user)->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_login_is_rate_limited(): void
    {
        User::factory()->create(['email' => 'rev@example.com']);

        foreach (range(1, 5) as $i) {
            $this->post('/admin/login', ['email' => 'rev@example.com', 'password' => 'wrong']);
        }

        $this->post('/admin/login', ['email' => 'rev@example.com', 'password' => 'wrong'])->assertStatus(429);
    }

    public function test_users_must_change_a_temporary_password_first(): void
    {
        $user = User::factory()->role('admin')->create(['must_change_password' => true]);

        $this->actingAs($user)->get('/admin')->assertRedirect(route('admin.profile.edit'));

        $this->actingAs($user)->put('/admin/profile/password', [
            'current_password' => 'Password-123',
            'password' => 'NewPassword-2026',
            'password_confirmation' => 'NewPassword-2026',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertFalse($user->fresh()->must_change_password);
        $this->actingAs($user->fresh())->get('/admin')->assertOk();
    }

    public function test_password_reset_does_not_reveal_whether_an_account_exists(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'rev@example.com']);

        $this->post('/admin/forgot-password', ['email' => 'nobody@example.com'])->assertSessionHas('status');
        $this->post('/admin/forgot-password', ['email' => 'rev@example.com'])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
        Notification::assertCount(1);
    }

    public function test_users_can_sign_out(): void
    {
        $this->actingAsRole('reviewer');

        $this->post('/admin/logout')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }
}
