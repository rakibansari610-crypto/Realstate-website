@extends('layouts.admin.master')
@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Create Visit</h4>
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

                        <form method="post" action="{{ route('org.visits.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="card mb-3">
                                <div class="card-header fw-semibold d-flex align-items-center justify-content-between">
                                    <span>Visitor</span>
                                    <small class="text-muted">Photo is optional but recommended</small>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        {{-- existing fields... --}}
                                       
                                        <div class="col-md-3">
                                            <label class="form-label">Name</label>
                                            <input name="v_name" class="form-control" value="{{ old('v_name') }}"
                                                placeholder="Visitor name">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Phone</label>
                                            <input name="v_phone" class="form-control" value="{{ old('v_phone') }}"
                                                placeholder="Phone">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="v_email" class="form-control"
                                                value="{{ old('v_email') }}" placeholder="Email">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Company</label>
                                            <input type="text" name="v_company" class="form-control"
                                                value="{{ old('company') }}" placeholder="Company">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">ID Type</label>
                                            <input name="v_id_type" class="form-control" value="{{ old('v_id_type') }}"
                                                placeholder="Aadhar/PAN/Passport">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">ID Number</label>
                                            <input name="v_id_number" class="form-control" value="{{ old('v_id_number') }}"
                                                placeholder="ID no.">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Vehicle No.</label>
                                            <input name="v_vehicle" class="form-control" value="{{ old('v_vehicle') }}"
                                                placeholder="Optional">
                                        </div>

                                        {{-- Webcam / Photo capture --}}
                                        <div class="col-md-6">
                                            <label class="form-label">Capture Photo (Webcam)</label>
                                            <div class="border rounded p-2">
                                                <div class="d-flex gap-3 align-items-start">
                                                    <video id="cam" autoplay playsinline muted
                                                        style="width:240px;height:180px;background:#000;border-radius:6px;"></video>
                                                    <canvas id="snapshot" width="240" height="180" class="d-none"
                                                        style="border-radius:6px;"></canvas>
                                                    <img id="photoPreview" class="d-none border rounded"
                                                        style="width:240px;height:180px;object-fit:cover;">
                                                </div>
                                                <div class="mt-2 d-flex gap-2">
                                                    <button type="button" id="btnStart"
                                                        class="btn btn-sm btn-outline-secondary">Start Camera</button>
                                                    <button type="button" id="btnCapture" class="btn btn-sm btn-primary"
                                                        disabled>Capture</button>
                                                    <button type="button" id="btnRetake"
                                                        class="btn btn-sm btn-warning d-none">Retake</button>
                                                    <button type="button" id="btnClear"
                                                        class="btn btn-sm btn-light d-none">Clear</button>
                                                </div>
                                                <input type="hidden" name="v_photo_base64" id="v_photo_base64">
                                                <small class="text-muted d-block mt-2">If camera is blocked, use file upload
                                                    below.</small>
                                            </div>
                                        </div>

                                        {{-- File fallback (mobile opens camera by default) --}}
                                        <div class="col-md-3">
                                            <label class="form-label">Upload Photo (Fallback)</label>
                                            <input type="file" name="v_photo_file" accept="image/*" capture="user"
                                                class="form-control">
                                            <small class="text-muted">Optional. Used only if you don't capture via
                                                webcam.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header fw-semibold">Visit</div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Site</label>
                                            <select name="site_id" class="form-select" required>
                                                <option value="">Select site</option>
                                                @foreach ($sites as $s)
                                                    <option value="{{ $s->id }}" @selected(old('site_id') == $s->id)>
                                                        {{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Host (Employee)</label>
                                            <select name="host_user_id" class="form-select" required>
                                                <option value="">Select host</option>
                                                @foreach ($hosts as $h)
                                                    <option value="{{ $h->id }}" @selected(old('host_user_id') == $h->id)>
                                                        {{ $h->name }} ({{ $h->email }})</option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <div class="col-md-3">
                                            <label class="form-label">Scheduled Start</label>
                                            <input type="datetime-local" name="scheduled_start" class="form-control"
                                                value="{{ old('scheduled_start') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Scheduled End</label>
                                            <input type="datetime-local" name="scheduled_end" class="form-control"
                                                value="{{ old('scheduled_end') }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Purpose</label>
                                            <input name="purpose" class="form-control" value="{{ old('purpose') }}"
                                                placeholder="Meeting, Interview, Delivery...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button class="btn btn-primary">Create & Send for Approval</button>
                                <a href="{{ route('org.visits.index') }}" class="btn btn-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            (() => {
                const video = document.getElementById('cam');
                const canvas = document.getElementById('snapshot');
                const preview = document.getElementById('photoPreview');
                const inputBase64 = document.getElementById('v_photo_base64');

                const btnStart = document.getElementById('btnStart');
                const btnCapture = document.getElementById('btnCapture');
                const btnRetake = document.getElementById('btnRetake');
                const btnClear = document.getElementById('btnClear');

                let stream = null;

                async function startCamera() {
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: 'user',
                                width: {
                                    ideal: 640
                                },
                                height: {
                                    ideal: 480
                                }
                            },
                            audio: false
                        });
                        video.srcObject = stream;
                        btnCapture.disabled = false;
                        preview.classList.add('d-none');
                        canvas.classList.add('d-none');
                        video.classList.remove('d-none');
                    } catch (e) {
                        alert('Unable to access camera: ' + e.message);
                    }
                }

                function stopCamera() {
                    if (stream) {
                        stream.getTracks().forEach(t => t.stop());
                        stream = null;
                    }
                    btnCapture.disabled = true;
                }

                function capture() {
                    const ctx = canvas.getContext('2d');
                    // Fit video into canvas size
                    const w = canvas.width,
                        h = canvas.height;
                    ctx.fillStyle = '#000';
                    ctx.fillRect(0, 0, w, h);
                    const vw = video.videoWidth,
                        vh = video.videoHeight;
                    // keep aspect ratio
                    const ratio = Math.min(w / vw, h / vh);
                    const nw = vw * ratio,
                        nh = vh * ratio;
                    const dx = (w - nw) / 2,
                        dy = (h - nh) / 2;
                    ctx.drawImage(video, dx, dy, nw, nh);

                    const data = canvas.toDataURL('image/jpeg', 0.9);
                    inputBase64.value = data;

                    canvas.classList.remove('d-none');
                    video.classList.add('d-none');
                    preview.src = data;
                    preview.classList.remove('d-none');

                    btnRetake.classList.remove('d-none');
                    btnClear.classList.remove('d-none');
                }

                function retake() {
                    inputBase64.value = '';
                    preview.classList.add('d-none');
                    canvas.classList.add('d-none');
                    video.classList.remove('d-none');
                }

                function clearPhoto() {
                    inputBase64.value = '';
                    preview.src = '';
                    preview.classList.add('d-none');
                    canvas.classList.add('d-none');
                    // keep camera state as-is
                }

                btnStart?.addEventListener('click', startCamera);
                btnCapture?.addEventListener('click', capture);
                btnRetake?.addEventListener('click', retake);
                btnClear?.addEventListener('click', clearPhoto);

                // Stop camera when leaving page
                window.addEventListener('beforeunload', stopCamera);
            })();
        </script>
    @endsection
