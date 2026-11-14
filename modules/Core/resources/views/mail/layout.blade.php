{{--
    Branded HTML e-mail layout (table-based, inline styles for e-mail clients).
    @extends('core::mail.layout')
    @section('preheader', 'Short preview text')
    @section('content') ... @endsection
    Helpers: @include('core::mail.button', ['url' => ..., 'label' => ...])
             @include('core::mail.panel', ['slot' => '...html...'])  (use @component with slot if preferred)
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light only">
    <title>{{ setting('site_name') }}</title>
</head>
<body style="margin:0;padding:0;background:#F2F5F9;font-family:'Inter','Segoe UI',Roboto,'Hind Siliguri',Arial,sans-serif;color:#0B1F3A;-webkit-font-smoothing:antialiased;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">@yield('preheader')</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F2F5F9;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;">
                    <tr>
                        <td style="background:#0B1F3A;border-radius:16px 16px 0 0;padding:26px 32px;">
                            <table role="presentation" cellpadding="0" cellspacing="0"><tr>
                                <td style="vertical-align:middle;padding-right:12px;">
                                    <img src="{{ asset('images/delta-mark.png') }}" width="34" height="30" alt="" style="display:block;border:0;">
                                </td>
                                <td style="vertical-align:middle;font-size:13px;letter-spacing:4px;color:#ffffff;">
                                    <strong>DELTA</strong> <span style="color:#4DBABD;">INITIATIVES</span>
                                </td>
                            </tr></table>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#ffffff;padding:36px 32px 28px;font-size:15px;line-height:1.65;color:#1C3150;">
                            @yield('content')
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#ffffff;border-top:1px solid #E1E7EF;border-radius:0 0 16px 16px;padding:20px 32px 26px;font-size:12px;line-height:1.6;color:#637A99;">
                            <strong style="color:#0B1F3A;">{{ setting('site_name') }}</strong> · {{ setting('site_descriptor') }}<br>
                            {{ setting('contact_address') }} · <a href="mailto:{{ setting('contact_email') }}" style="color:#137174;">{{ setting('contact_email') }}</a><br>
                            <em style="color:#0B1F3A;">{{ setting('site_tagline') }}</em>
                            @hasSection('footer')
                                <br><br>@yield('footer')
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
