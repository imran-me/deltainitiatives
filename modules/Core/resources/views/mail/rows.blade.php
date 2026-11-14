{{-- Key/value summary box. @include('core::mail.rows', ['rows' => ['Idea ID' => 'DI-2026-0001', ...], 'mono' => ['Fingerprint']]) --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;background:#F3FAFA;border:1px solid #BFE6E7;border-radius:12px;">
    @foreach($rows as $label => $value)
        <tr>
            <td style="padding:12px 16px 4px;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#137174;font-weight:600;">{{ $label }}</td>
        </tr>
        <tr>
            <td style="padding:0 16px 12px;font-size:{{ in_array($label, $mono ?? [], true) ? '12px' : '15px' }};color:#0B1F3A;{{ in_array($label, $mono ?? [], true) ? "font-family:Consolas,'SFMono-Regular',monospace;word-break:break-all;" : 'font-weight:600;' }}{{ ! $loop->last ? 'border-bottom:1px solid #E6F5F5;' : '' }}">{{ $value }}</td>
        </tr>
    @endforeach
</table>
