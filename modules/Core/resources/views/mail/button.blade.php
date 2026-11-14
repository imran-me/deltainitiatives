{{-- @include('core::mail.button', ['url' => $url, 'label' => 'Open']) --}}
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:24px 0;">
    <tr>
        <td style="background:#0B1F3A;border-radius:999px;">
            <a href="{{ $url }}" style="display:inline-block;padding:12px 26px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:999px;">{{ $label }}</a>
        </td>
    </tr>
</table>
