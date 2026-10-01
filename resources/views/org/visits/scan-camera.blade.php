@extends('layouts.admin.master')
@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Scan Visitor QR</h4>
                @role('receptionist')
                    <a href="@role('receptionist'){{ route('receptionist.dashboard') }}@endrole" class="btn btn-primary">Home</a>
                    @endrole
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body" style="min-height: 550px;">


                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="row mt-2">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">

                                    <div class="mb-3">
                                        <div class="d-flex gap-2 align-items-center flex-wrap">
                                            <button id="btnStart" type="button" class="btn btn-primary btn-sm">Start
                                                Camera</button>
                                            <button id="btnSwitch" type="button" class="btn btn-outline-secondary btn-sm"
                                                disabled>Switch Camera</button>
                                            <button id="btnStop" type="button" class="btn btn-light btn-sm"
                                                disabled>Stop</button>
                                            <span id="status" class="ms-2 text-muted small">Idle</span>
                                        </div>
                                    </div>

                                    <div id="reader"
                                        style="max-width:480px;width:100%;border:1px solid #eee;border-radius:10px;"></div>

                                    <hr class="my-4">

                                    <h6>Manual / USB Scanner Input</h6>
                                    <p class="text-muted small mb-2">
                                        If you’re using a USB/Bluetooth QR scanner (keyboard-wedge), focus the box below and
                                        scan.
                                        You can also paste a full pass URL; we’ll extract the token automatically.
                                    </p>

                                    <form method="get" action="{{ route('org.visits.scan') }}" class="d-flex gap-2">
                                        <input id="manualToken" name="token" class="form-control"
                                            placeholder="Paste token or scan with USB scanner, then Enter">
                                        <button class="btn btn-outline-primary">Open</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- html5-qrcode (CDN) --}}
                    <script src="https://unpkg.com/html5-qrcode" defer></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const GO_URL = @json(route('org.visits.scan', [], false)); // relative path (works with subfolders)
                            const readerEl = document.getElementById('reader');
                            const btnStart = document.getElementById('btnStart');
                            const btnStop = document.getElementById('btnStop');
                            const btnSwitch = document.getElementById('btnSwitch');
                            const statusEl = document.getElementById('status');
                            const manual = document.getElementById('manualToken');

                            let html5Qr = null;
                            let cameras = [];
                            let currentCamIndex = 0;

                            const setStatus = (txt, ok = false) => {
                                statusEl.textContent = txt;
                                statusEl.className = ok ? 'ms-2 small text-success' : 'ms-2 small text-muted';
                            };

                            const extractToken = (text) => {
                                try {
                                    // full URL with ?token=
                                    const u = new URL(text);
                                    const t = u.searchParams.get('token');
                                    if (t) return t;
                                    // /pass/{token}
                                    const parts = u.pathname.split('/').filter(Boolean);
                                    const passIdx = parts.indexOf('pass');
                                    if (passIdx !== -1 && parts[passIdx + 1]) return parts[passIdx + 1];
                                } catch (_) {
                                    /* not a full URL */
                                }

                                // otherwise treat as raw token
                                return text;
                            };

                            const go = (raw) => {
                                const token = extractToken(raw).trim();
                                if (!token) return;
                                // redirect to scan endpoint
                                window.location.href = GO_URL + '?token=' + encodeURIComponent(token);
                            };

                            // manual Enter (also works with hardware scanner)
                            manual.addEventListener('keydown', (e) => {
                                if (e.key === 'Enter') {
                                    e.preventDefault();
                                    go(manual.value);
                                }
                            });

                            async function startCamera(index = 0) {
                                if (!window.Html5Qrcode) {
                                    setStatus('Scanner library not loaded yet…');
                                    return;
                                }
                                try {
                                    if (!html5Qr) html5Qr = new Html5Qrcode('reader', {
                                        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
                                    });

                                    // enumerate cameras on first run
                                    if (!cameras.length) {
                                        cameras = await Html5Qrcode.getCameras();
                                        if (!cameras.length) throw new Error('No cameras found');
                                    }
                                    currentCamIndex = (index >= 0 && index < cameras.length) ? index : 0;
                                    const camId = cameras[currentCamIndex].id;

                                    await html5Qr.start(
                                        camId, {
                                            fps: 12,
                                            qrbox: 250
                                        },
                                        (decodedText) => {
                                            setStatus('Scanned ✓', true);
                                            stopCamera().then(() => go(decodedText));
                                        },
                                        () => {} // ignore scan errors per frame
                                    );

                                    btnStop.disabled = false;
                                    btnSwitch.disabled = cameras.length < 2;
                                    setStatus('Camera running…');
                                } catch (e) {
                                    setStatus('Camera error: ' + e.message);
                                }
                            }

                            function stopCamera() {
                                if (html5Qr && html5Qr.isScanning) {
                                    btnStop.disabled = true;
                                    btnSwitch.disabled = true;
                                    return html5Qr.stop().then(() => {
                                        html5Qr.clear();
                                        setStatus('Stopped');
                                    }).catch(err => setStatus('Stop error: ' + err));
                                }
                                return Promise.resolve();
                            }

                            btnStart.addEventListener('click', () => startCamera());
                            btnStop.addEventListener('click', () => stopCamera());
                            btnSwitch.addEventListener('click', () => {
                                const next = (currentCamIndex + 1) % cameras.length;
                                stopCamera().then(() => startCamera(next));
                            });

                            // Try to auto-start after first click (helps iOS permission)
                            document.addEventListener('click', function kickoff() {
                                document.removeEventListener('click', kickoff);
                                // no auto-start to avoid surprise; user will press Start
                            }, {
                                once: true
                            });
                        });
                    </script>
                @endsection
