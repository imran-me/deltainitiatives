<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Crypt;
use Modules\Users\Models\User;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Tests never depend on built front-end assets.
        $this->withoutVite();
    }

    /**
     * Anti-spam fields every public form needs (honeypot empty + time-trap).
     * Turnstile is disabled in phpunit.xml.
     *
     * @return array<string, string>
     */
    protected function formProtection(): array
    {
        return [
            'hp_company_website' => '',
            'hp_rendered' => Crypt::encryptString((string) (time() - 30)),
        ];
    }

    /** Chainable variant: $this->asRole('editor')->get('/admin'). */
    protected function asRole(string $role = 'super_admin'): static
    {
        $this->actingAsRole($role);

        return $this;
    }

    /** Sign in as a fresh active user with the given role. */
    protected function actingAsRole(string $role = 'super_admin'): User
    {
        $user = User::factory()->role($role)->create();
        $this->actingAs($user);

        return $user;
    }
}
