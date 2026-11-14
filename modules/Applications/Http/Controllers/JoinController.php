<?php

namespace Modules\Applications\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Applications\Http\Requests\JoinRequest;
use Modules\Applications\Mail\ApplicationReceived;
use Modules\Applications\Mail\NewApplicationAlert;
use Modules\Applications\Models\JoinApplication;
use Throwable;

/**
 * Join Us — serves both /join-us (English) and /bn/join-us (Bangla).
 */
class JoinController extends Controller
{
    public function create(Request $request): View
    {
        $role = $request->query('role');

        return view('applications::join', [
            'selectedRole' => in_array($role, JoinApplication::ROLES, true) ? $role : null,
        ]);
    }

    public function store(JoinRequest $request): RedirectResponse
    {
        $data = Arr::only($request->payload(), [
            'role', 'name', 'phone', 'institution', 'study_level', 'department', 'city',
            'linkedin_url', 'skills', 'motivation', 'availability',
        ]);

        $cvPath = $request->hasFile('cv')
            ? $request->file('cv')->storeAs('applications', Str::uuid().'.pdf', 'local')
            : null;

        $application = JoinApplication::query()->create($data + [
            'email' => mb_strtolower(trim($request->validated('email'))),
            'cv_path' => $cvPath,
            'locale' => app()->getLocale(),
            'consent_at' => now(),
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        try {
            Mail::to($application->email, $application->name)->queue(new ApplicationReceived($application));
            Mail::to(setting('contact_email'))->queue(new NewApplicationAlert($application));
        } catch (Throwable $e) {
            // The application is safely stored; a mail outage must not lose it.
            report($e);
        }

        return redirect()->to(localized_route('join.create').'#apply')
            ->with('success', __('applications::join.form.success', ['reference' => $application->reference()]));
    }
}
