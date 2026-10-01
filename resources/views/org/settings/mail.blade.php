@extends('layouts.admin.master')
@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Email (SMTP) Settings</h4>
                <div class="page-title-right">
                    <button type="button" onclick="history.back()" class="btn btn-light">Back</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div>


                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Fix the errors below:</strong>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="post" action="{{ route('org.mail.update') }}" class="card mb-4">
                            @csrf @method('PUT')
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">SMTP Host</label>
                                        <input name="mail_host" class="form-control"
                                            value="{{ old('mail_host', $setting->mail_host) }}" required>
                                        <small class="text-muted">e.g., smtp.office365.com</small>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Port</label>
                                        <input name="mail_port" type="number" class="form-control"
                                            value="{{ old('mail_port', $setting->mail_port ?? 587) }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Encryption</label>
                                        <select name="mail_encryption" class="form-select">
                                            <option value="" @selected(!$setting->mail_encryption)>None</option>
                                            <option value="tls" @selected($setting->mail_encryption === 'tls')>TLS (STARTTLS)</option>
                                            <option value="ssl" @selected($setting->mail_encryption === 'ssl')>SSL (Implicit)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Username</label>
                                        <input name="mail_username" class="form-control"
                                            value="{{ old('mail_username', $setting->mail_username) }}" autocomplete="off">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Password</label>
                                        <input name="mail_password" type="password" class="form-control" minlength="1"
                                            autocomplete="new-password" autocapitalize="none" spellcheck="false"
                                            placeholder="Leave blank to keep existing">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">From Address</label>
                                        <input type="email" name="mail_from_address" class="form-control"
                                            value="{{ old('mail_from_address', $setting->mail_from_address) }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">From Name</label>
                                        <input name="mail_from_name" class="form-control"
                                            value="{{ old('mail_from_name', $setting->mail_from_name ?? config('app.name')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex gap-2">
                                <button class="btn btn-primary">Save</button>
                                <a href="{{ url()->previous() }}" class="btn btn-light">Cancel</a>
                            </div>
                        </form>

                        {{-- Test sender --}}
                        <div class="card">
                            <div class="card-header fw-semibold">Send Test Email</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">To (Email)</label>
                                        <input id="test_to" type="email" class="form-control"
                                            placeholder="you@example.com">
                                    </div>
                                    <div class="col-md-9">
                                        <small class="text-muted">
                                            Test uses the current form values (not .env). If you changed fields above, you
                                            don’t have to save to test.
                                        </small>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button id="btnTest" class="btn btn-secondary">Send Test</button>
                                    <span id="testResult" class="ms-3"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                
                
                        <script>
                            document.getElementById('btnTest').addEventListener('click', async function() {
                                const btn = this,
                                    resEl = document.getElementById('testResult');
                                btn.disabled = true;
                                resEl.textContent = 'Sending...';

                                const payload = {
                                    mail_host: document.querySelector('[name="mail_host"]').value,
                                    mail_port: document.querySelector('[name="mail_port"]').value,
                                    mail_username: document.querySelector('[name="mail_username"]').value,
                                    mail_password: document.querySelector('[name="mail_password"]').value,
                                    mail_encryption: document.querySelector('[name="mail_encryption"]').value || null,
                                    mail_from_address: document.querySelector('[name="mail_from_address"]').value,
                                    mail_from_name: document.querySelector('[name="mail_from_name"]').value,
                                    test_to: document.getElementById('test_to').value,
                                    _token: '{{ csrf_token() }}',
                                };

                                try {
                                    const resp = await fetch('{{ route('org.mail.test') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify(payload)
                                    });
                                    const json = await resp.json();
                                    if (resp.ok && json.ok) {
                                        resEl.textContent = json.message;
                                        resEl.className = 'text-success';
                                    } else {
                                        resEl.textContent = 'Failed: ' + (json.message || 'Unknown error');
                                        resEl.className = 'text-danger';
                                    }
                                } catch (e) {
                                    resEl.textContent = 'Error: ' + e.message;
                                    resEl.className = 'text-danger';
                                } finally {
                                    btn.disabled = false;
                                }
                            });
                        </script>
                  
@endsection