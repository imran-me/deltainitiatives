@extends('core::mail.layout')

@section('preheader', __('ideaportal::portal.mail.receipt_preheader', ['code' => $idea->code]))

@section('content')
    <p style="margin:0 0 6px;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#137174;font-weight:600;">{{ __('ideaportal::portal.mail.receipt_eyebrow') }}</p>
    <h1 style="margin:0 0 14px;font-size:22px;line-height:1.3;color:#0B1F3A;">{{ __('ideaportal::portal.mail.receipt_heading', ['name' => \Illuminate\Support\Str::before($idea->lead_name, ' ')]) }}</h1>
    <p style="margin:0 0 6px;">{{ __('ideaportal::portal.mail.receipt_intro') }}</p>

    @include('core::mail.rows', [
        'rows' => [
            __('ideaportal::portal.receipt.id') => $idea->code,
            __('ideaportal::portal.receipt.idea') => $idea->title,
            __('ideaportal::portal.receipt.submitted') => dhaka_time($idea->submitted_at, 'j M Y, g:i:s A'),
            __('ideaportal::portal.receipt.fingerprint') => $idea->fingerprint,
            __('ideaportal::portal.receipt.agreement') => 'v'.$idea->agreement_version,
        ],
        'mono' => [__('ideaportal::portal.receipt.fingerprint')],
    ])

    @if($files->isNotEmpty())
        <p style="margin:18px 0 6px;font-weight:600;color:#0B1F3A;">{{ __('ideaportal::portal.mail.files') }}</p>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;">
            @foreach($files as $file)
                <tr>
                    <td style="padding:6px 0;border-bottom:1px solid #E1E7EF;color:#1C3150;">{{ $file->original_name }} <span style="color:#637A99;">({{ $file->humanSize() }})</span><br>
                        <span style="font-family:Consolas,'SFMono-Regular',monospace;font-size:11px;color:#637A99;word-break:break-all;">SHA-256 {{ $file->sha256 }}</span></td>
                </tr>
            @endforeach
        </table>
    @endif

    <p style="margin:22px 0 6px;font-weight:600;color:#0B1F3A;">{{ __('ideaportal::portal.mail.next_title') }}</p>
    <p style="margin:0;">{{ __('ideaportal::portal.mail.next_text') }}</p>

    @include('core::mail.button', ['url' => $verifyUrl, 'label' => __('ideaportal::portal.mail.verify_button')])

    <p style="margin:0;font-size:13px;color:#637A99;">{{ __('ideaportal::portal.mail.keep', ['email' => setting('ideas_email')]) }}</p>
@endsection
