@extends('core::mail.layout')

@section('preheader', 'New partner inquiry from '.$inquiry->organization)

@section('content')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:#0B1F3A;">New partner inquiry</h1>
    <p style="margin:0 0 8px;">A new inquiry arrived through <em>Partner with Us</em>. Reply directly to this e-mail to answer {{ $inquiry->contact_name }}.</p>

    @include('core::mail.rows', ['rows' => array_filter([
        'Organisation' => $inquiry->organization,
        'Contact' => $inquiry->contact_name.($inquiry->job_title ? ' — '.$inquiry->job_title : ''),
        'E-mail' => $inquiry->email,
        'Phone' => $inquiry->phone,
        'Website' => $inquiry->website,
        'Country' => $inquiry->country,
        'Interest' => $inquiry->partnershipType?->name ?? 'Not specified',
        'Received' => dhaka_time($inquiry->created_at),
    ])])

    <p style="margin:0 0 6px;font-size:13px;color:#637A99;">Message:</p>
    <p style="margin:0 0 8px;padding:14px 16px;background:#F2F5F9;border-radius:10px;font-size:14px;white-space:pre-line;">{{ $inquiry->message }}</p>

    @include('core::mail.button', ['url' => route('admin.resources.show', ['partner-inquiries', $inquiry->getKey()]), 'label' => 'Open in the admin panel'])
@endsection
