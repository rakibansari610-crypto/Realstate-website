@php
    $mode = $mode ?? 'create';
    $user = $user ?? null;
    $roleSelected = old('role', $currentRole ?? ($roles[0] ?? 'employee'));

@endphp

<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Country *</label>
        <select name="country_id" class="form-select" required>
            <option value="">— Select Country —</option>
            @foreach($countries as $country)
                <option value="{{ $country->id }}"
                    @selected(old('country_id', $user->country_id ?? '') == $country->id)>
                    {{ $country->country_name }}
                </option>
            @endforeach
        </select>
    </div>


    <div class="col-md-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
            @foreach ($roles as $r)
                <option value="{{ $r }}" @selected($roleSelected === $r)>{{ $r === 'rm' ? 'Relationship Manager' : ucfirst($r) }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Site (optional)</label>
        <select name="site_id" class="form-select">
            <option value="">— None —</option>
            @foreach ($sites as $s)
                <option value="{{ $s->id }}" @selected(old('site_id', $user->site_id ?? '') == $s->id)>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>

    @if ($mode === 'create')
        <div class="col-md-3">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" minlength="6" autocomplete="new-password"
                autocapitalize="none" spellcheck="false" required>
        </div>
    @else
        <div class="col-md-3">
            <label class="form-label">Password (leave blank to keep)</label>
            <input name="password" type="password" class="form-control" minlength="6" autocomplete="new-password"
                autocapitalize="none" spellcheck="false">
        </div>
    @endif
</div>
