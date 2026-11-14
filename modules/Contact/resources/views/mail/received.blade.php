@extends('core::mail.layout')

@section('preheader', __('contact::contact.mail.preheader'))

@section('content')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:#0B1F3A;">{{ __('contact::contact.mail.heading', ['name' => \Illuminate\Support\Str::before($msg->name, ' ')]) }}</h1>
    <p style="margin:0 0 8px;">{{ __('contact::contact.mail.intro') }}</p>

    @include('core::mail.rows', ['rows' => [
        __('contact::contact.mail.reference') => $msg->reference(),
        __('contact::contact.mail.topic') => $msg->topicLabel(),
        __('contact::contact.mail.subject_label') => $msg->subject,
    ]])

    <p style="margin:0 0 16px;">{{ __('contact::contact.mail.next') }}</p>

    @if($msg->topic === 'ideas')
        @include('core::mail.button', ['url' => route('ideas.create'), 'label' => __('contact::contact.mail.idea_button')])
    @endif

    <p style="margin:0;font-size:13px;color:#637A99;">{{ __('contact::contact.mail.footer') }}</p>
@endsection
