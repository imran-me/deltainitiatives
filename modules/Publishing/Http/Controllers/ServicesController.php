<?php

namespace Modules\Publishing\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Publishing\Http\Requests\ExpertApplicationRequest;
use Modules\Publishing\Http\Requests\ServiceRequestRequest;
use Modules\Publishing\Models\ExpertApplication;
use Modules\Publishing\Models\ServiceRequest;

class ServicesController extends Controller
{
    public function proposal(ServiceRequestRequest $request): RedirectResponse
    {
        $data = $request->payload();

        ServiceRequest::query()->create([
            'service' => $data['service'],
            'name' => trim($data['name']),
            'organization' => trim($data['organization']),
            'email' => mb_strtolower(trim($data['email'])),
            'phone' => $data['phone'] ?? null,
            'budget' => $data['budget'] ?? null,
            'message' => trim($data['message']),
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->to(url()->previous().'#proposal')
            ->with('success', 'Thank you. We have received your request and will reply within three working days.');
    }

    public function expert(ExpertApplicationRequest $request): RedirectResponse
    {
        $data = $request->payload();

        ExpertApplication::query()->create([
            'name' => trim($data['name']),
            'email' => mb_strtolower(trim($data['email'])),
            'field' => $data['field'],
            'institution' => $data['institution'] ?? null,
            'expertise' => trim($data['expertise']),
            'profile_url' => $data['profile_url'] ?? null,
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->to(route('services.expert-network').'#join')
            ->with('success', 'Thank you for applying. We review every profile and will be in touch.');
    }
}
