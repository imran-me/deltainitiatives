@extends('core::mail.layout')

@section('preheader', 'New '.$application->roleLabel().' application from '.$application->name)

@section('content')
    <p style="margin:0 0 4px;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#137174;font-weight:600;">Join Us · New application</p>
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:#0B1F3A;">{{ $application->name }} wants to join as {{ $application->roleLabel() }}</h1>

    @include('core::mail.rows', ['rows' => [
        'Reference' => $application->reference(),
        'Role' => $application->roleLabel(),
        'E-mail' => $application->email,
        'Institution' => $application->institution,
        'City' => $application->city,
        'CV attached' => $application->cv_path ? 'Yes — download it from the admin panel' : 'No',
        'Language' => $application->locale === 'bn' ? 'Bangla' : 'English',
    ]])

    @include('core::mail.button', ['url' => $adminUrl, 'label' => 'Open in admin'])

    <p style="margin:0;font-size:13px;color:#637A99;">Reply to this e-mail to write to the applicant directly. Personal details stay in the admin panel — please don't forward them.</p>
@endsection
