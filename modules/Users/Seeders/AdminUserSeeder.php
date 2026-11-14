<?php

namespace Modules\Users\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Users\Models\User;

/**
 * Creates the first Super Admin from ADMIN_EMAIL / ADMIN_PASSWORD (.env).
 * If ADMIN_PASSWORD is empty a strong one-time password is generated and printed,
 * and the account must choose a new password at first sign-in.
 *
 * In the local environment only, it also creates one demo account per role
 * (…@delta.test, password from DEMO_PASSWORD) so every permission level can be tried.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = mb_strtolower((string) env('ADMIN_EMAIL', 'admin@deltainitiatives.com'));

        if (! User::query()->where('email', $email)->exists()) {
            $password = (string) env('ADMIN_PASSWORD');
            $generated = $password === '';
            $password = $generated ? Str::password(20) : $password;

            User::query()->create([
                'name' => env('ADMIN_NAME', 'Delta Admin'),
                'email' => $email,
                'password' => $password,
                'role' => 'super_admin',
                'is_active' => true,
                'must_change_password' => $generated,
            ]);

            $this->command?->getOutput()->writeln("  <info>Super Admin:</info> {$email}".($generated ? "  one-time password: <comment>{$password}</comment>" : ''));
        }

        if (app()->environment('local')) {
            $demoPassword = (string) env('DEMO_PASSWORD', 'Delta-Demo-2026');

            foreach (['admin' => 'Demo Administrator', 'reviewer' => 'Demo Reviewer', 'editor' => 'Demo Content Editor', 'community' => 'Demo Community Lead'] as $role => $name) {
                User::query()->firstOrCreate(['email' => "{$role}@delta.test"], [
                    'name' => $name,
                    'password' => $demoPassword,
                    'role' => $role,
                    'is_active' => true,
                ]);
            }

            $this->command?->getOutput()->writeln("  <info>Local demo accounts:</info> admin@ / reviewer@ / editor@ / community@delta.test  password: <comment>{$demoPassword}</comment>");
        }
    }
}
