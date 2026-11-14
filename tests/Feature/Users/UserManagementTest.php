<?php

namespace Tests\Feature\Users;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Modules\Users\Models\User;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_users_with_users_manage_can_open_user_management(): void
    {
        foreach (['admin', 'reviewer', 'editor', 'community'] as $role) {
            $this->asRole($role)->get('/admin/users')->assertForbidden();
        }

        $this->asRole('super_admin')->get('/admin/users')->assertOk()->assertSee('Users &amp; roles', false);
    }

    public function test_super_admin_invites_a_reviewer_with_their_own_account(): void
    {
        Notification::fake();
        $this->actingAsRole('super_admin');

        $this->post('/admin/users', [
            'name' => 'Nusrat Reviewer',
            'email' => 'Nusrat@Example.com',
            'role' => 'reviewer',
            'invite' => '1',
        ])->assertRedirect(route('admin.users.index'));

        $user = User::query()->where('email', 'nusrat@example.com')->firstOrFail();
        $this->assertSame('reviewer', $user->role);
        $this->assertTrue($user->must_change_password);
        Notification::assertSentTo($user, ResetPassword::class);
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.created', 'subject_id' => $user->id]);
    }

    public function test_you_cannot_demote_or_deactivate_yourself(): void
    {
        $me = $this->actingAsRole('super_admin');

        $this->put("/admin/users/{$me->id}", ['name' => $me->name, 'email' => $me->email, 'role' => 'reviewer', 'is_active' => '1'])
            ->assertSessionHas('error');

        $this->assertSame('super_admin', $me->fresh()->role);
    }

    public function test_a_super_admin_can_change_another_super_admin_while_one_remains(): void
    {
        $this->actingAsRole('super_admin');
        $other = User::factory()->superAdmin()->create();

        $this->put("/admin/users/{$other->id}", ['name' => $other->name, 'email' => $other->email, 'role' => 'admin', 'is_active' => '0'])
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame('admin', $other->fresh()->role);
        $this->assertFalse($other->fresh()->is_active);
        $this->assertSame(1, User::query()->where('role', 'super_admin')->where('is_active', true)->count());
    }

    public function test_role_permissions_match_configuration(): void
    {
        $reviewer = User::factory()->role('reviewer')->create();
        $editor = User::factory()->role('editor')->create();

        $this->assertTrue($reviewer->can('ideas.score'));
        $this->assertFalse($reviewer->can('content.manage'));
        $this->assertFalse($reviewer->can('ideas.export'));
        $this->assertTrue($editor->can('content.manage'));
        $this->assertFalse($editor->can('ideas.view'));
        $this->assertTrue(User::factory()->superAdmin()->create()->can('backups.manage'));
    }
}
