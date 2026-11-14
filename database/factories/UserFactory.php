<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Users\Models\User;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('Password-123'),
            'remember_token' => Str::random(10),
            'role' => 'reviewer',
            'is_active' => true,
            'must_change_password' => false,
        ];
    }

    public function role(string $role): static
    {
        return $this->state(fn () => ['role' => $role]);
    }

    public function superAdmin(): static
    {
        return $this->role('super_admin');
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
