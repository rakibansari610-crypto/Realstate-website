@php
  // $site (optional), $orgs provided by controller
@endphp
<div class="row g-3">
  <div class="col-md-3">
    <label class="form-label">Organization</label>
    <select name="org_id" class="form-select" required>
      <option value="">Select organization</option>
      @foreach($orgs as $o)
        <option value="{{ $o->id }}" @selected(old('org_id', $site->org_id ?? '') == $o->id)>{{ $o->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">Name</label>
    <input name="name" class="form-control" value="{{ old('name', $site->name ?? '') }}" required>
  </div>
  <div class="col-md-3">
    <label class="form-label">Code (optional)</label>
    <input name="code" class="form-control" value="{{ old('code', $site->code ?? '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">Timezone</label>
    <input name="timezone" class="form-control" value="{{ old('timezone', $site->timezone ?? 'UTC') }}">
    <small class="text-muted">e.g., Asia/Kolkata</small>
  </div>
  <div class="col-12">
    <label class="form-label">Address (JSON, optional)</label>
    <textarea name="address" class="form-control" rows="3" placeholder='{"line1":"...", "city":"..."}'>{{ old('address', isset($site->address) ? json_encode($site->address, JSON_PRETTY_PRINT) : '') }}</textarea>
  </div>
  <div class="col-12">
    <label class="form-label">Settings (JSON, optional)</label>
    <textarea name="settings" class="form-control" rows="3" placeholder='{"checkin":{"require_otp":true}}'>{{ old('settings', isset($site->settings) ? json_encode($site->settings, JSON_PRETTY_PRINT) : '') }}</textarea>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('submit', function(e){
  const jsonFields = ['address','settings'];
  for (const f of jsonFields) {
    const el = document.querySelector(`[name="${f}"]`);
    if (el && el.value.trim().length) {
      try { JSON.parse(el.value); }
      catch(err) { e.preventDefault(); alert(f.toUpperCase()+' must be valid JSON.'); return; }
    }
  }
});
</script>
@endpush
