<?php

namespace Modules\Users\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Core\Services\AuditLogger;
use Modules\Users\Models\User;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.$request->input('q').'%')
                ->orWhere('email', 'like', '%'.$request->input('q').'%')))
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->input('role')))
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('users::admin.index', ['users' => $users, 'roles' => config('users.roles')]);
    }

    public function create(): View
    {
        return view('users::admin.form', ['user' => new User(['role' => 'reviewer', 'is_active' => true]), 'roles' => config('users.roles')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'title' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')],
            'role' => ['required', Rule::in(array_keys(config('users.roles')))],
            'invite' => ['nullable', 'boolean'],
        ]);

        $this->guardRoleAssignment($request, $data['role']);

        // Each reviewer gets their own account. A random password is set and the
        // person chooses their own via the e-mailed link (never shared passwords).
        $temporary = Str::password(20);

        $user = User::query()->create([
            'name' => $data['name'],
            'title' => $data['title'] ?? null,
            'email' => mb_strtolower($data['email']),
            'role' => $data['role'],
            'password' => $temporary,
            'is_active' => true,
            'must_change_password' => true,
        ]);

        audit('user.created', $user, "Created account for {$user->email} as {$user->roleLabel()}", ['role' => $user->role]);

        if ($request->boolean('invite', true)) {
            Password::sendResetLink(['email' => $user->email]);

            return redirect()->route('admin.users.index')->with('success', "Account created. {$user->name} has been e-mailed a link to set their password.");
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Account created. Temporary password (shown once): {$temporary}");
    }

    public function edit(User $user): View
    {
        return view('users::admin.form', ['user' => $user, 'roles' => config('users.roles')]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'title' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys(config('users.roles')))],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->guardRoleAssignment($request, $data['role']);

        $isActive = $request->boolean('is_active');

        if ($user->is($request->user()) && (! $isActive || $data['role'] !== $user->role)) {
            return back()->with('error', 'You cannot deactivate yourself or change your own role.');
        }

        if ($user->isSuperAdmin() && ($data['role'] !== 'super_admin' || ! $isActive)
            && User::query()->where('role', 'super_admin')->active()->count() <= 1) {
            return back()->with('error', 'There must always be at least one active Super Admin.');
        }

        $user->fill([
            'name' => $data['name'],
            'title' => $data['title'] ?? null,
            'email' => mb_strtolower($data['email']),
            'role' => $data['role'],
            'is_active' => $isActive,
        ])->save();

        audit('user.updated', $user, "Updated account {$user->email}", app(AuditLogger::class)->diff($user));

        return redirect()->route('admin.users.index')->with('success', 'Account updated.');
    }

    public function sendReset(User $user): RedirectResponse
    {
        Password::sendResetLink(['email' => $user->email]);
        audit('user.reset_link_sent', $user, "Sent password reset link to {$user->email}");

        return back()->with('success', "A password reset link has been sent to {$user->email}.");
    }

    protected function guardRoleAssignment(Request $request, string $role): void
    {
        abort_if($role === 'super_admin' && ! $request->user()->isSuperAdmin(), 403, 'Only a Super Admin can grant Super Admin access.');
    }
}
