@extends('core::mail.layout')

@section('preheader', ($registration->isWaitlisted() ? 'You are on the waitlist for ' : 'See you at ').$event->title)

@section('content')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:#0B1F3A;">
        {{ $registration->isWaitlisted() ? 'You’re on the waitlist' : 'You’re registered' }}
    </h1>
    <p style="margin:0 0 8px;">Hello {{ $registration->name }},</p>
    @if($registration->isWaitlisted())
        <p style="margin:0 0 8px;">Thank you for your interest in <strong>{{ $event->title }}</strong>. The event is currently full, so we have added you to the waitlist. If a seat opens up we will e-mail you straight away.</p>
    @else
        <p style="margin:0 0 8px;">Thank you for registering for <strong>{{ $event->title }}</strong>. Your seat is confirmed — here are the details.</p>
    @endif

    @include('core::mail.rows', ['rows' => array_filter([
        'Event' => $event->title,
        'Date' => $event->dateLabel(),
        'Time' => $event->timeLabel(),
        'Where' => $event->whereLabel(),
        'Status' => \Modules\Programmes\Models\EventRegistration::STATUSES[$registration->status] ?? $registration->status,
    ])])

    @if($event->online_note && in_array($event->mode, ['online', 'hybrid'], true))
        <p style="margin:0 0 8px;">{{ $event->online_note }}</p>
    @endif

    @include('core::mail.button', ['url' => route('events.show', $event), 'label' => 'View event details'])

    <p style="margin:0;font-size:13px;color:#637A99;">Can no longer attend, or need any access support? Just reply to this e-mail so we can offer your seat to someone else.</p>
@endsection
