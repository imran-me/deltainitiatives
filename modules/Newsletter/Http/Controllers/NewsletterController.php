<?php

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Modules\Newsletter\Http\Requests\SubscribeRequest;
use Modules\Newsletter\Mail\ConfirmSubscription;
use Modules\Newsletter\Models\Subscriber;
use Throwable;

/**
 * Double opt-in newsletter: subscribe -> confirmation e-mail -> confirmed.
 * Every e-mail carries a one-click unsubscribe link.
 */
class NewsletterController extends Controller
{
    public function store(SubscribeRequest $request): RedirectResponse
    {
        $data = $request->payload();
        $email = mb_strtolower(trim($data['email']));
        $source = $data['source'] ?? 'footer';

        $subscriber = Subscriber::query()->firstOrNew(['email' => $email]);

        if ($subscriber->status !== 'subscribed') {
            $subscriber->fill([
                'status' => 'pending',
                'token' => $subscriber->token ?? Subscriber::newToken(),
                'source' => $subscriber->source ?? $source,
                'locale' => app()->getLocale(),
                'ip_address' => $request->ip(),
                'unsubscribed_at' => null,
            ])->save();

            try {
                Mail::to($subscriber->email)->queue(new ConfirmSubscription($subscriber));
            } catch (Throwable $e) {
                report($e);
            }
        }

        // Same message whether or not the address was already subscribed.
        return redirect()->to(url()->previous().'#newsletter-'.$source)
            ->with('newsletter', ['source' => $source, 'message' => __('newsletter::newsletter.check_inbox')]);
    }

    public function confirm(string $token): View
    {
        $subscriber = Subscriber::query()->where('token', $token)->firstOrFail();

        if ($subscriber->status !== 'subscribed') {
            $subscriber->forceFill(['status' => 'subscribed', 'confirmed_at' => now(), 'unsubscribed_at' => null])->save();
        }

        return view('newsletter::status', [
            'title' => __('newsletter::newsletter.confirmed_title'),
            'message' => __('newsletter::newsletter.confirmed_text'),
            'icon' => 'mail-check',
        ]);
    }

    public function unsubscribe(string $token): View
    {
        $subscriber = Subscriber::query()->where('token', $token)->firstOrFail();

        if ($subscriber->status !== 'unsubscribed') {
            $subscriber->forceFill(['status' => 'unsubscribed', 'unsubscribed_at' => now()])->save();
        }

        return view('newsletter::status', [
            'title' => __('newsletter::newsletter.unsubscribed_title'),
            'message' => __('newsletter::newsletter.unsubscribed_text'),
            'icon' => 'mail-x',
        ]);
    }
}
