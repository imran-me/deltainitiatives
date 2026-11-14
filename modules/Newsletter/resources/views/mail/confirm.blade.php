@extends('core::mail.layout')

@section('preheader', __('newsletter::newsletter.mail_preheader'))

@section('content')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:#0B1F3A;">{{ __('newsletter::newsletter.mail_heading') }}</h1>
    <p style="margin:0 0 8px;">{{ __('newsletter::newsletter.mail_body') }}</p>
    @include('core::mail.button', ['url' => $confirmUrl, 'label' => __('newsletter::newsletter.mail_button')])
    <p style="margin:0;font-size:13px;color:#637A99;">{{ __('newsletter::newsletter.mail_ignore') }}</p>
@endsection

@section('footer')
    <a href="{{ $unsubscribeUrl }}" style="color:#637A99;">{{ __('newsletter::newsletter.unsubscribe') }}</a>
@endsection
