<?php

namespace Modules\Users\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;
use Modules\Admin\Support\AdminNav;
use Modules\Core\Services\AuditLogger;
use Modules\Core\Support\ModuleServiceProvider;
use Modules\Users\Models\User;

class UsersServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'Users';

    protected string $alias = 'users';

    public function boot(): void
    {
        parent::boot();

        $this->registerGates();
        $this->registerAuditListeners();

        Password::defaults(fn () => $this->app->isProduction()
            ? Password::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised()
            : Password::min(10)->letters()->mixedCase()->numbers());

        ResetPassword::toMailUsing(fn (User $user, string $token) => (new MailMessage)
            ->subject('Set or reset your Delta Initiatives admin password')
            ->greeting("Hello {$user->name},")
            ->line('Use the button below to choose a password for your Delta Initiatives admin account.')
            ->action('Choose a password', route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->line('This link expires in 60 minutes. If you did not expect this e-mail, you can ignore it — your account stays unchanged.'));

        AdminNav::add([
            'group' => 'System', 'label' => 'Users & roles', 'route' => 'admin.users.index',
            'active' => 'admin.users.*', 'icon' => 'users-round', 'permission' => 'users.manage', 'order' => 10,
        ]);
    }

    protected function registerGates(): void
    {
        Gate::before(fn (User $user) => $user->is_active && $user->isSuperAdmin() ? true : null);

        foreach (array_keys(config('users.permissions', [])) as $permission) {
            Gate::define($permission, fn (User $user) => $user->is_active && $user->hasPermission($permission));
        }
    }

    protected function registerAuditListeners(): void
    {
        Event::listen(function (Login $event) {
            if ($event->user instanceof User) {
                $event->user->forceFill(['last_login_at' => now(), 'last_login_ip' => request()->ip()])->saveQuietly();
                app(AuditLogger::class)->log('auth.login', $event->user, 'Signed in', [], $event->user->id);
            }
        });

        Event::listen(function (Logout $event) {
            if ($event->user instanceof User) {
                app(AuditLogger::class)->log('auth.logout', $event->user, 'Signed out', [], $event->user->id);
            }
        });

        Event::listen(function (Failed $event) {
            app(AuditLogger::class)->log('auth.login_failed', $event->user, 'Failed sign-in attempt', [
                'email' => mb_substr((string) ($event->credentials['email'] ?? ''), 0, 120),
            ]);
        });

        Event::listen(function (Lockout $event) {
            app(AuditLogger::class)->log('auth.lockout', null, 'Too many sign-in attempts — temporarily locked', [
                'email' => mb_substr((string) $event->request->input('email'), 0, 120),
            ]);
        });

        Event::listen(function (PasswordReset $event) {
            if ($event->user instanceof User) {
                app(AuditLogger::class)->log('auth.password_reset', $event->user, 'Password reset via e-mail link', [], $event->user->id);
            }
        });
    }
}
