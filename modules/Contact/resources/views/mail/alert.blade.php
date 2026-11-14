@extends('core::mail.layout')

@section('preheader', 'New message from '.$msg->name.': '.$msg->subject)

@section('content')
    <p style="margin:0 0 4px;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#137174;font-weight:600;">Contact form · {{ $msg->topicLabel() }}</p>
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:#0B1F3A;">{{ $msg->subject }}</h1>

    @include('core::mail.rows', ['rows' => array_filter([
        'Reference' => $msg->reference(),
        'From' => $msg->name.' <'.$msg->email.'>',
        'Organisation' => $msg->organization,
        'Phone' => $msg->phone,
    ])])

    <div style="margin:0 0 8px;padding:16px 18px;border-left:3px solid #1FA2A6;background:#F8FAFC;border-radius:0 12px 12px 0;color:#1C3150;white-space:pre-line;">{{ $msg->message }}</div>

    @include('core::mail.button', ['url' => $adminUrl, 'label' => 'Open in admin'])

    <p style="margin:0;font-size:13px;color:#637A99;">Reply to this e-mail to answer {{ $msg->name }} directly, then mark the message as “Replied” in the admin panel.</p>
@endsection
