@extends('core::mail.layout')

@section('preheader', 'We have received your message and will reply within three working days.')

@section('content')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:#0B1F3A;">Thank you for reaching out</h1>
    <p style="margin:0 0 8px;">Dear {{ $inquiry->contact_name }},</p>
    <p style="margin:0 0 8px;">Thank you for your interest in partnering with Delta Initiatives. Your message has reached our partnerships team, and a member of the team will reply within <strong>three working days</strong>.</p>

    @include('core::mail.rows', ['rows' => array_filter([
        'Organisation' => $inquiry->organization,
        'Area of interest' => $inquiry->partnershipType?->name ?? 'Not sure yet — let’s talk',
        'Received' => dhaka_time($inquiry->created_at),
    ])])

    <p style="margin:0 0 6px;font-size:13px;color:#637A99;">Your message:</p>
    <p style="margin:0 0 20px;padding:14px 16px;background:#F2F5F9;border-radius:10px;font-size:14px;white-space:pre-line;">{{ $inquiry->message }}</p>

    <p style="margin:0;">If anything is urgent, simply reply to this e-mail.</p>
    <p style="margin:16px 0 0;">With thanks,<br><strong>The Delta Initiatives team</strong></p>
@endsection
