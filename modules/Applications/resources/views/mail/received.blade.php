@extends('core::mail.layout')

@section('preheader', __('applications::join.mail.preheader'))

@section('content')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:#0B1F3A;">{{ __('applications::join.mail.heading', ['name' => \Illuminate\Support\Str::before($application->name, ' ')]) }}</h1>
    <p style="margin:0 0 8px;">{{ __('applications::join.mail.intro', ['role' => $application->roleLabel()]) }}</p>

    @include('core::mail.rows', ['rows' => [
        __('applications::join.mail.reference') => $application->reference(),
        __('applications::join.mail.role') => $application->roleLabel(),
        __('applications::join.mail.submitted') => dhaka_time($application->created_at),
    ]])

    <p style="margin:0 0 6px;font-weight:600;color:#0B1F3A;">{{ __('applications::join.mail.next_title') }}</p>
    <p style="margin:0 0 16px;">{{ __('applications::join.mail.next') }}</p>

    @include('core::mail.button', ['url' => route('programmes.index'), 'label' => __('applications::join.mail.button')])

    <p style="margin:0;font-size:13px;color:#637A99;">{{ __('applications::join.mail.questions', ['email' => setting('contact_email')]) }}</p>
@endsection
