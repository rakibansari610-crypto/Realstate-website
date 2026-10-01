@php $brand = $brand ?? '#4F46E5'; @endphp
<a href="{{ $href }}"
   style="display:inline-block;background:{{ $brand }};color:#ffffff;text-decoration:none;
          padding:12px 16px;border-radius:10px;font-weight:700">
  {{ $slot }}
</a>
<p style="margin:14px 0 0 0;font-size:12px;color:#6B7280;">
  If the button doesn't work, copy and paste this link:<br>
  <a href="{{ $href }}" style="color:{{ $brand }};text-decoration:underline;">{{ $href }}</a>
</p>
