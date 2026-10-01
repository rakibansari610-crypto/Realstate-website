@extends('emails.layouts.base', [
  'brand'     => $brand ?? '#4F46E5',
  'fromName'  => $fromName ?? 'Visitor Suite',
  'preheader' => $preheader ?? '',
])

@section('title', 'Your visit is scheduled')
@section('subtitle', 'Show your QR pass at reception on arrival.')

@section('content')
  @php $muted='#6B7280'; $textColor='#0B1225'; @endphp
  <tr>
    <td style="padding:12px 24px 8px 24px;">
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
             style="background:#F9FAFB;border:1px solid #EEF2FF;border-radius:12px;">
        <tr>
          <td style="padding:14px 16px;">
            <div style="font-size:16px;color:{{ $textColor }}"><strong>Organization:</strong> {{ $orgName }}</div>
            <div style="font-size:14px;color:{{ $muted }};margin-top:4px;"><strong>Host:</strong> {{ $hostName }}</div>
            <div style="font-size:14px;color:{{ $muted }};margin-top:4px;"><strong>Site:</strong> {{ $siteName }}</div>
            <div style="font-size:14px;color:{{ $muted }};margin-top:4px;"><strong>Purpose:</strong> {{ $purpose ?? '—' }}</div>
            <div style="font-size:14px;color:{{ $muted }};margin-top:4px;"><strong>When:</strong> {{ $whenStr }}</div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td style="padding:4px 24px 8px 24px;">
      <p style="margin:12px 0 0 0;font-size:12px;color:{{ $muted }};">
        We've attached a calendar invite (visit.ics) for your convenience.
      </p>
    </td>
  </tr>
@endsection

@section('cta')
  @component('emails.components.button', ['href' => $passUrl, 'brand' => $brand ?? '#4F46E5'])
    Open Your QR Pass
  @endcomponent
@endsection

@section('footer')
Thanks,<br>{{ $fromName }}<br>
<a href="{{ $hostUrl }}" style="color:#94A3B8;text-decoration:none;">{{ $hostUrl }}</a>
@endsection
