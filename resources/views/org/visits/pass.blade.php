@php
  // Safe photo URL
  $photoUrl = $visit->visitor?->photo_path ? asset('storage/'.$visit->visitor->photo_path) : null;
  $orgName  = $visit->org?->name ?? config('app.name');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Visitor Pass #{{ $visit->id }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
  :root { --fg:#111; --muted:#666; --brand:#0ea5e9; }
  html,body{margin:0;padding:0;background:#f5f7fb;font-family:system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial,sans-serif;color:var(--fg);}
  /* Card size ~ ID badge / A7; prints nicely on A4 as well */
  .sheet { width: 90mm; min-height: 60mm; margin: 10mm auto; }
  .pass {
    background:#fff; border:1px solid #e5e7eb; border-radius:10px;
    box-shadow:0 4px 18px rgba(0,0,0,.06); overflow:hidden;
  }
  .hdr{display:flex; align-items:center; gap:10px; padding:10px 12px; border-bottom:1px dashed #e5e7eb;}
  .brand{font-weight:700; letter-spacing:.2px;}
  .content{display:grid; grid-template-columns:1fr 1fr; gap:12px; padding:12px;}
  .field{margin:2px 0;}
  .label{font-size:11px; color:var(--muted); display:block;}
  .value{font-weight:600; font-size:13px;}
  .photo{
    width: 100%; aspect-ratio: 3/4; object-fit: cover; border-radius:8px;
    background:#eee; border:1px solid #e5e7eb;
  }
  .qr{display:flex; align-items:center; justify-content:center; width:100%; aspect-ratio:1/1; border:1px solid #e5e7eb; border-radius:8px;}
  .footer{padding:10px 12px; display:flex; justify-content:space-between; align-items:center; border-top:1px dashed #e5e7eb;}
  .badge{padding:4px 8px; border-radius:999px; font-size:11px; font-weight:700}
  .pending{background:#fff7ed; color:#9a3412;}
  .approved{background:#eef6ff; color:#1e40af;}
  .checked_in{background:#eef2ff; color:#3730a3;}
  .checked_out{background:#ecfdf5; color:#065f46;}
  .denied{background:#fee2e2; color:#991b1b;}
  .printbar{display:flex; gap:8px; justify-content:center; margin:12px 0;}
  .btn{padding:8px 12px; border-radius:8px; border:1px solid #e5e7eb; background:#fff; cursor:pointer}
  .btn-primary{background:var(--brand); color:#fff; border-color:var(--brand)}
  @media print{
    body{background:#fff}
    .printbar{display:none}
    .sheet{margin:0; width:86mm; min-height:54mm;}
  }
</style>
</head>
<body>

<div class="printbar">
  <button class="btn btn-primary" onclick="window.print()">Print</button>
  <button class="btn" onclick="window.close()">Close</button>
</div>

<div class="sheet">
  <div class="pass">
    <div class="hdr">
      {{-- Optional org logo left, if you have one in settings --}}
      <div style="width:28px;height:28px; border-radius:6px; background:#e0f2fe; display:flex;align-items:center;justify-content:center;color:#0369a1;font-weight:800">V</div>
      <div>
        <div class="brand">{{ $orgName }}</div>
        <div style="font-size:11px;color:#64748b;">Visitor Pass • #{{ $visit->id }}</div>
      </div>
      <div style="margin-left:auto">
        @php
          $class = match($visit->status){
            'pending'=>'pending','approved'=>'approved','checked_in'=>'checked_in','checked_out'=>'checked_out','denied'=>'denied', default=>'pending'
          };
        @endphp
        <span class="badge {{ $class }}">{{ strtoupper(str_replace('_',' ',$visit->status)) }}</span>
      </div>
    </div>

    <div class="content">
      <div>
        <div class="field">
          <span class="label">Visitor</span>
          <span class="value">{{ $visit->visitor?->name ?? '—' }}</span>
        </div>
        <div class="field">
          <span class="label">Host</span>
          <span class="value">{{ $visit->host?->name ?? '—' }}</span>
        </div>
        <div class="field">
          <span class="label">Site</span>
          <span class="value">{{ $visit->site?->name ?? '—' }}</span>
        </div>
        <div class="field">
          <span class="label">Purpose</span>
          <span class="value">{{ $visit->purpose ?? '—' }}</span>
        </div>
        <div class="field">
          <span class="label">Valid Until</span>
          <span class="value">
            {{ $validUntil?->format('Y-m-d H:i') }}
          </span>
        </div>
      </div>

      <div>
        @if ($photoUrl)
          <img src="{{ $photoUrl }}" class="photo" alt="Visitor photo">
        @else
          <div class="photo" style="display:flex;align-items:center;justify-content:center;color:#999;font-size:12px;">No Photo</div>
        @endif
      </div>

      <div class="qr">
        {{-- QR SVG (token) --}}
        {!! QrCode::size(140)->margin(0)->generate($visit->qr_token) !!}
      </div>

      <div>
        <div class="field">
          <span class="label">Pass Token</span>
          <span class="value" style="word-break:break-all">{{ $visit->qr_token }}</span>
        </div>
        <div class="field">
          <span class="label">Created</span>
          <span class="value">{{ $visit->created_at?->format('Y-m-d H:i') }}</span>
        </div>
        @if($visit->checked_in_at)
          <div class="field">
            <span class="label">Checked In</span>
            <span class="value">{{ $visit->checked_in_at?->format('Y-m-d H:i') }}</span>
          </div>
        @endif
        @if($visit->checked_out_at)
          <div class="field">
            <span class="label">Checked Out</span>
            <span class="value">{{ $visit->checked_out_at?->format('Y-m-d H:i') }}</span>
          </div>
        @endif
      </div>
    </div>

    <div class="footer">
      <span style="font-size:11px;color:#64748b;">Please display this pass at all times.</span>
      <span style="font-size:11px;color:#64748b;">{{ request()->getHost() }}</span>
    </div>
  </div>
</div>

</body>
</html>
