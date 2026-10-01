@php
  $brand     = $brand ?? '#4F46E5';
  $muted     = '#6B7280';
  $textColor = '#0B1225';
  $preheader = $preheader ?? '';
@endphp
<!doctype html>
<html>
  <body style="margin:0;padding:0;background:#F5F7FB;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;">
    {{-- Preheader (hidden) --}}
    @if($preheader)
      <div style="display:none;max-height:0;overflow:hidden;color:transparent;opacity:0;">{{ $preheader }}</div>
    @endif

    <div style="max-width:640px;margin:0 auto;padding:24px;">
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
             style="background:#ffffff;border-radius:16px;box-shadow:0 6px 24px rgba(17,24,39,0.08);overflow:hidden;">
        <tr>
          <td style="padding:24px 24px 8px 24px;">
            <h2 style="margin:0 0 6px 0;font-size:20px;font-weight:800;color:{{ $textColor }};">@yield('title')</h2>
            @hasSection('subtitle')
              <p style="margin:0;color:{{ $muted }};font-size:14px;">@yield('subtitle')</p>
            @endif
          </td>
        </tr>

        @yield('content')

        @hasSection('cta')
          <tr>
            <td style="padding:16px 24px 24px 24px;">
              @yield('cta')
            </td>
          </tr>
        @endif
      </table>

      <div style="text-align:center;color:#94A3B8;font-size:12px;margin-top:12px;">
        
        @yield('footer', 'Sent by ' . ($fromName ?? 'Visitor Suite'))
      </div>
    </div>
  </body>
</html>
