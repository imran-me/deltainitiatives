<?php

namespace Modules\Contact\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Modules\Contact\Http\Requests\ContactRequest;
use Modules\Contact\Mail\MessageReceived;
use Modules\Contact\Mail\NewContactMessageAlert;
use Modules\Contact\Models\ContactMessage;
use Throwable;

class ContactController extends Controller
{
    public function create(Request $request): View
    {
        $topic = $request->query('topic');

        $socials = array_filter([
            'linkedin' => ['LinkedIn', setting('social_linkedin')],
            'facebook' => ['Facebook', setting('social_facebook')],
            'x-brand' => ['X', setting('social_x')],
            'youtube' => ['YouTube', setting('social_youtube')],
            'instagram' => ['Instagram', setting('social_instagram')],
        ], fn ($s) => filled($s[1]));

        return view('contact::contact', [
            'selectedTopic' => in_array($topic, ContactMessage::TOPICS, true) ? $topic : null,
            'socials' => $socials,
        ]);
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $message = ContactMessage::query()->create(
            Arr::only($request->payload(), ['topic', 'name', 'phone', 'organization', 'subject', 'message']) + [
                'email' => mb_strtolower(trim($request->validated('email'))),
                'status' => 'new',
                'locale' => app()->getLocale(),
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
            ]
        );

        try {
            Mail::to($message->email, $message->name)->queue(new MessageReceived($message));
            Mail::to(setting('contact_email'))->queue(new NewContactMessageAlert($message));
        } catch (Throwable $e) {
            // The message is safely stored; a mail outage must not lose it.
            report($e);
        }

        return redirect()->to(route('contact.create').'#message')
            ->with('success', __('contact::contact.form.success', ['reference' => $message->reference()]));
    }
}
