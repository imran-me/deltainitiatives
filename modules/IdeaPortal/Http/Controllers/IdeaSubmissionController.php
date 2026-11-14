<?php

namespace Modules\IdeaPortal\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\CMS\Models\FocusArea;
use Modules\Core\Services\SettingsService;
use Modules\IdeaPortal\Http\Requests\SubmitIdeaRequest;
use Modules\IdeaPortal\Services\SubmissionService;

/**
 * Public Idea Portal: /submit-an-idea and /bn/submit-an-idea.
 */
class IdeaSubmissionController extends Controller
{
    public function create(SettingsService $settings): View
    {
        return view('ideaportal::create', [
            'open' => $settings->bool('ideas_open', true),
            'focusAreas' => FocusArea::options(),
            'questions' => config('ideaportal.questions'),
            'files' => config('ideaportal.files'),
        ]);
    }

    public function store(SubmitIdeaRequest $request, SubmissionService $service, SettingsService $settings): RedirectResponse
    {
        if (! $settings->bool('ideas_open', true)) {
            return redirect()->to(localized_route('ideas.create'))->with('error', __('ideaportal::portal.closed.title'));
        }

        $idea = $service->submit($request->payload(), $request->file('files') ?? [], $request);

        return redirect()->to(localized_route('ideas.received'))->with('idea_receipt', [
            'code' => $idea->code,
            'title' => $idea->title,
            'submitted_at' => $idea->submitted_at->toIso8601String(),
            'fingerprint' => $idea->fingerprint,
            'email' => $idea->lead_email,
        ]);
    }

    /** Shows the receipt once, straight after submitting. No ID in the URL, so nothing to enumerate. */
    public function received(): View|RedirectResponse
    {
        $receipt = session('idea_receipt');

        if (! is_array($receipt) || empty($receipt['code'])) {
            return redirect()->to(localized_route('ideas.create'));
        }

        return view('ideaportal::received', ['receipt' => $receipt]);
    }
}
