@extends('core::mail.layout')

@section('preheader', 'A new idea has been submitted through the Idea Portal.')

@section('content')
    <p style="margin:0 0 6px;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#137174;font-weight:600;">Idea Portal</p>
    <h1 style="margin:0 0 14px;font-size:22px;line-height:1.3;color:#0B1F3A;">A new idea has arrived</h1>
    <p style="margin:0;">A team has submitted an idea. It has been recorded, fingerprinted and receipted.</p>

    @include('core::mail.rows', [
        'rows' => [
            'Idea ID' => $idea->code,
            'Title' => $idea->title,
            'Focus area' => $idea->focus_area_name ?: '—',
            'Submitted' => dhaka_time($idea->submitted_at),
        ],
    ])

    @include('core::mail.button', ['url' => $adminUrl, 'label' => 'Open in the admin panel'])

    <p style="margin:0;font-size:13px;color:#637A99;">For confidentiality, the idea itself is only available inside the admin panel to signed-in reviewers. Please don't forward this e-mail outside the team.</p>
@endsection
