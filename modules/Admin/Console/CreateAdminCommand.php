<?php

namespace Modules\Admin\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Users\Models\User;

class CreateAdminCommand extends Command
{
    protected $signature = 'delta:create-admin
        {email : E-mail address for the account}
        {--name= : Full name}
        {--role=super_admin : Role key (super_admin, admin, reviewer, editor, community)}';

    protected $description = 'Create an admin account with a one-time password that must be changed at first sign-in';

    public function handle(): int
    {
        $email = mb_strtolower($this->argument('email'));
        $role = $this->option('role');

        if (! array_key_exists($role, config('users.roles'))) {
            $this->components->error("Unknown role [{$role}].");

            return self::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->components->error('An account with that e-mail already exists.');

            return self::FAILURE;
        }

        $password = Str::password(20);

        User::query()->create([
            'name' => $this->option('name') ?: Str::of($email)->before('@')->headline(),
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $this->components->info("Created {$role} account for {$email}.");
        $this->line("  One-time password: <fg=yellow>{$password}</>");
        $this->line('  Sign in at '.route('admin.login').' — you will be asked to choose a new password.');

        return self::SUCCESS;
    }
}
