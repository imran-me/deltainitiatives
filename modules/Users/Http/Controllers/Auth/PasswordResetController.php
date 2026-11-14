<?php

namespace Modules\Users\Http\Controllers\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;
use Modules\Core\Rules\Turnstile;
use Modules\Users\Models\User;

class PasswordResetController extends Controller
{
    public function request(): View
    {
        return view('users::auth.forgot-password');
    }

    public function email(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'cf-turnstile-response' => [new Turnstile],
        ]);

        $user = User::query()->active()->where('email', mb_strtolower(trim($request->input('email'))))->first();

        if ($user) {
            Password::sendResetLink(['email' => $user->email]);
            audit('auth.password_reset_requested', $user, 'Password reset link requested');
        }

        // Same response whether or not the account exists (no account enumeration).
        return back()->with('status', 'If that e-mail belongs to an active account, a reset link is on its way. It expires in 60 minutes.');
    }

    public function edit(Request $request, string $token): View
    {
        return view('users::auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                    'must_change_password' => false,
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('status', 'Your password has been reset. You can sign in now.')
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
