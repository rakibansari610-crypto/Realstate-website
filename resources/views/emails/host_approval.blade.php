@extends('emails.layouts.base', [
  'brand'     => $brand ?? '#4F46E5',
  'fromName'  => $fromName ?? 'Visitor Suite',
  'preheader' => $preheader ?? '',
])

@section('title', 'Visitor awaiting your approval')
@section('subtitle', 'Please review the details and approve or deny.')

@section('content')
  @php $muted='#6B7280'; $textColor='#0B1225'; @endphp
  <tr>
    <td style="padding:12px 24px 8px 24px;">
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
             style="background:#F9FAFB;border:1px solid #EEF2FF;border-radius:12px;">
        <tr>
          <td style="padding:14px 16px;">
            <div style="font-size:16px;color:{{ $textColor }}"><strong>Visitor:</strong> {{ $visitorName }}</div>
            <div style="font-size:14px;color:{{ $muted }};margin-top:4px;"><strong>Site:</strong> {{ $siteName }}</div>
            <div style="font-size:14px;color:{{ $muted }};margin-top:4px;"><strong>Purpose:</strong> {{ $purpose ?? '—' }}</div>
            <div style="font-size:14px;color:{{ $muted }};margin-top:4px;"><strong>When:</strong> {{ $whenStr }}</div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
@endsection

@section('cta')
  @component('emails.components.button', ['href' => $approveUrl, 'brand' => $brand ?? '#4F46E5'])
    Review & Approve
  @endcomponent
@endsection
