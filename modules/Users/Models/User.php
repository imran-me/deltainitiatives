<?php

namespace Modules\Users\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * An admin-panel account (team member, reviewer, editor…).
 * Public visitors never have accounts.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'title', 'is_active', 'must_change_password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    /** @return array<int, string> */
    public function permissions(): array
    {
        return config("users.roles.{$this->role}.permissions", []);
    }

    public function hasPermission(string $permission): bool
    {
        $granted = $this->permissions();

        return in_array('*', $granted, true) || in_array($permission, $granted, true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function roleLabel(): string
    {
        return config("users.roles.{$this->role}.label", ucfirst((string) $this->role));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Users whose role grants the given permission. */
    public function scopeWithPermission(Builder $query, string $permission): Builder
    {
        $roles = collect(config('users.roles'))
            ->filter(fn ($role) => in_array('*', $role['permissions'], true) || in_array($permission, $role['permissions'], true))
            ->keys()
            ->all();

        return $query->whereIn('role', $roles);
    }

    /** @return array<string, string> */
    public static function roleOptions(): array
    {
        return collect(config('users.roles'))->map(fn ($r) => $r['label'])->all();
    }
}
