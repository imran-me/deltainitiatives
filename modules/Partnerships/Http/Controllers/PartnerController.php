<?php

namespace Modules\Partnerships\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Partnerships\Http\Requests\PartnerInquiryRequest;
use Modules\Partnerships\Mail\PartnerInquiryAcknowledgement;
use Modules\Partnerships\Mail\PartnerInquiryAlert;
use Modules\Partnerships\Models\PartnerInquiry;
use Modules\Partnerships\Models\PartnershipType;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class PartnerController extends Controller
{
    public const BROCHURE_FILENAME = 'Delta-Initiatives-Partnership-Brochure.pdf';

    public function index(Request $request): View
    {
        $types = PartnershipType::query()->published()->ordered()->get();
        $requested = (string) $request->query('type', '');

        return view('partnerships::index', [
            'types' => $types,
            'typeOptions' => $types->pluck('name', 'slug')->all(),
            'preselect' => $types->contains('slug', $requested) ? $requested : null,
            'hasBrochure' => $this->brochurePath() !== null,
        ]);
    }

    public function store(PartnerInquiryRequest $request): RedirectResponse
    {
        $data = $request->payload();

        $inquiry = PartnerInquiry::query()->create([
            'organization' => trim($data['organization']),
            'contact_name' => trim($data['contact_name']),
            'job_title' => $data['job_title'] ?? null,
            'email' => mb_strtolower(trim($data['email'])),
            'phone' => $data['phone'] ?? null,
            'website' => $data['website'] ?? null,
            'country' => filled($data['country'] ?? null) ? trim($data['country']) : 'Bangladesh',
            'partnership_type_id' => filled($data['partnership_type'] ?? null)
                ? PartnershipType::query()->where('slug', $data['partnership_type'])->value('id')
                : null,
            'message' => trim($data['message']),
            'status' => 'new',
            'locale' => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]);

        try {
            Mail::to($inquiry->email)->queue(new PartnerInquiryAcknowledgement($inquiry));

            if ($team = setting('partners_email')) {
                Mail::to($team)->queue(new PartnerInquiryAlert($inquiry));
            }
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()->to(route('partner.index').'#inquiry')->with('success',
            'Thank you, '.$inquiry->contact_name.'. We have received your message and will reply within three working days. A copy is on its way to '.$inquiry->email.'.');
    }

    /** Streams the partnership brochure uploaded in Admin → Settings, or asks for it via the form. */
    public function brochure(): StreamedResponse|RedirectResponse
    {
        $path = $this->brochurePath();

        if ($path === null) {
            return redirect()->to(route('partner.index').'#inquiry')->with('warning',
                'Our partnership brochure is being finalised. Tell us a little about your organisation below and we will send it to you as soon as it is ready.');
        }

        return Storage::disk('local')->response($path, self::BROCHURE_FILENAME, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'public, max-age=3600',
        ], 'inline');
    }

    protected function brochurePath(): ?string
    {
        $path = setting('brochure_path');

        return filled($path) && Storage::disk('local')->exists($path) ? $path : null;
    }
}
