<?php

namespace Modules\IdeaPortal\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\IdeaPortal\Http\Requests\VerifyReceiptRequest;
use Modules\IdeaPortal\Models\IdeaSubmission;

/**
 * Public receipt check: does this Idea ID + fingerprint match a recorded
 * submission? Answers yes/no and the recorded time only — never content.
 */
class VerifyReceiptController extends Controller
{
    public function show(): View
    {
        return view('ideaportal::verify', ['result' => session('verify_result')]);
    }

    public function check(VerifyReceiptRequest $request): RedirectResponse
    {
        $data = $request->payload();

        $idea = IdeaSubmission::query()
            ->where('code', $data['code'])
            ->first(['id', 'code', 'fingerprint', 'submitted_at']);

        $match = $idea !== null && hash_equals((string) $idea->fingerprint, $data['fingerprint']);

        return redirect()->route('ideas.verify')
            ->withInput($request->only('code', 'fingerprint'))
            ->with('verify_result', [
                'match' => $match,
                'code' => $data['code'],
                'time' => $match ? dhaka_time($idea->submitted_at, 'j F Y, g:i:s A') : null,
            ]);
    }
}
