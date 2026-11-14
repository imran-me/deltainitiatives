<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('users::profile', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'title' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => [Rule::requiredIf(fn () => mb_strtolower($request->input('email')) !== $user->email), 'nullable', 'current_password'],
        ]);

        $user->fill([
            'name' => $data['name'],
            'title' => $data['title'] ?? null,
            'email' => mb_strtolower($data['email']),
        ])->save();

        audit('user.profile_updated', $user, 'Updated own profile', app(\Modules\Core\Services\AuditLogger::class)->diff($user));

        return back()->with('success', 'Profile updated.');
    }

    public function password(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::defaults()],
        ]);

        $user->forceFill([
            'password' => $request->input('password'),
            'must_change_password' => false,
        ])->save();

        // Sign out every other session that used the old password.
        Auth::logoutOtherDevices($request->input('password'));
        $request->session()->regenerate();

        audit('user.password_changed', $user, 'Changed own password');

        return redirect()->route('admin.dashboard')->with('success', 'Password updated. Other sessions have been signed out.');
    }
}
