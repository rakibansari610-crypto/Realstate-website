@php
    $org = $org ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="{{ old('name', $org->name ?? '') }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">Slug (optional)</label>
        <input name="slug" class="form-control" value="{{ old('slug', $org->slug ?? '') }}">
        <small class="text-muted">Auto-generated if left blank.</small>
    </div>

    <div class="col-md-3">
        <label class="form-label">Subdomain</label>
        <input name="subdomain" class="form-control" value="{{ old('subdomain', $org->subdomain ?? '') }}" required>
        <small class="text-muted">Lowercase letters/numbers/dashes (e.g., <em>acme</em>).</small>
    </div>

    <div class="col-md-3">
        <label class="form-label">Custom Domain (optional)</label>
        <input name="custom_domain" class="form-control" value="{{ old('custom_domain', $org->custom_domain ?? '') }}">
        <small class="text-muted">e.g., visitorsuite.acme.com</small>
    </div>

    <div class="col-12">
        <label class="form-label">Settings (JSON, optional)</label>
        <textarea name="settings" class="form-control" rows="3" placeholder='{"brand":{"color":"#0ea5e9"}}'>{{ old('settings', isset($org->settings) ? json_encode($org->settings, JSON_PRETTY_PRINT) : '') }}</textarea>
        <small class="text-muted">Optional per-organization config.</small>
    </div>
</div>
<hr class="my-4">



<div id="adminFields" class="mt-3">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Admin Name</label>
            <input name="admin_name" class="form-control" value="{{ old('admin_name', $org->admin_name ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Admin Email</label>
            <input name="admin_email" type="email" class="form-control"
                value="{{ old('admin_email', $org->admin_email ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Admin Password</label>
            <input name="admin_password" type="password" class="form-control" minlength="6" autocomplete="new-password"
                autocapitalize="none" spellcheck="false" />
        </div>
    </div>

</div>

@push('scripts')
    <script>
        // Optional: Validate simple JSON in the settings textarea before submit
        document.addEventListener('submit', function(e) {
            const ta = document.querySelector('textarea[name="settings"]');
            if (ta && ta.value.trim().length) {
                try {
                    JSON.parse(ta.value);
                } catch (err) {
                    e.preventDefault();
                    alert('Settings must be valid JSON.');
                }
            }
        });
    </script>
@endpush
