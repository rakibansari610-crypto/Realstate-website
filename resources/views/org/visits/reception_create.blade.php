@extends('layouts.admin.master')
@section('main-content')
    <style>
        /* Wizard header */
        .vs-wizard-steps {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .vs-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            min-width: 64px;
        }

        .vs-step .circle {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            font-weight: 700;
            color: #0b1225;
            background: #c7d2fe;
            border: 2px solid transparent;
            transition: .15s ease;
        }

        .vs-step .circle.active {
            background: #60a5fa;
            color: #001022;
            border-color: #bfdbfe;
            box-shadow: 0 4px 14px rgba(59, 130, 246, .4);
        }

        .vs-step .circle.done {
            background: #34d399;
            color: #032618;
            border-color: #a7f3d0;
        }

        .vs-step .label {
            color: #eaf1ff;
            font-size: .9rem;
            font-weight: 600;
        }

        .vs-wizard-steps .line {
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, rgba(255, 255, 255, .2), rgba(255, 255, 255, .05));
        }

        /* Step panes */
        .vs-step-pane {
            display: none;
            opacity: 0;
            transform: translateY(8px);
            transition: opacity .15s ease, transform .15s ease;
        }

        .vs-step-pane.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        /* Review */
        .vs-review .row {
            margin: 0;
        }

        .vs-review-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 2px 0;
            border-bottom: 1px dashed rgba(255, 255, 255, .08);
        }

        .vs-review-item .k {
            color: #c9d7ff;
        }

        .vs-review-item .v {
            color: #fff;
            font-weight: 600;
        }

        /* Buttons focus */
        .vs-prev:focus,
        .vs-next:focus {
            outline: 3px solid #93c5fd;
            outline-offset: 2px;
        }
    </style>
    <div class="vs-reception-header">
        <div class="vs-greeting">
            <h1>Good {{ now()->format('H') < 12 ? 'Morning' : (now()->format('H') < 18 ? 'Afternoon' : 'Evening') }}
                <span>👋</span>
            </h1>
            <h2>{{ Auth::user()->name }}</h2>
        </div>

        <div class="vs-user-meta d-flex align-items-center gap-3">
            <!-- Clock -->
            <div id="live-clock" class="fw-bold text-white" style="font-size: 32px;"></div>

            @role('receptionist')
                <a title="logout" class="btn btn-danger" style="padding: 5px 10px;" href="{{ route('receptionist.dashboard') }}">
                    <i class="las la-home" style="font-size: 34px;"></i>
                </a>
            @endrole
        </div>
    </div>
    <div class="vs-panels" style="grid-template-columns:none">
        <div class="vs-panel">
            <div class="vs-panel-title d-flex align-items-center justify-content-between">
                <span>Create Visit</span>
                <small class="text-muted">3-step wizard</small>
            </div>

            {{-- Progress bar / steps --}}
            <div class="vs-wizard-steps mb-3">
                <div class="vs-step">
                    <div class="circle active" data-step="1">1</div>
                    <div class="label">Visitor</div>
                </div>
                <div class="line"></div>
                <div class="vs-step">
                    <div class="circle" data-step="2">2</div>
                    <div class="label">Visit</div>
                </div>
                <div class="line"></div>
                <div class="vs-step">
                    <div class="circle" data-step="3">3</div>
                    <div class="label">Review</div>
                </div>
            </div>

            <form id="visitWizardForm" method="post" action="{{ route('org.visits.store') }}"
                enctype="multipart/form-data">
                @csrf

                {{-- STEP 1: Visitor --}}
                <fieldset class="vs-step-pane active" data-step="1" aria-labelledby="step1-title">
                    <div class="card mb-3">
                        <div id="step1-title"
                            class="card-header fw-semibold d-flex align-items-center justify-content-between">
                            <span>Visitor</span>
                            <small class="text-muted">Photo is optional but recommended</small>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-9">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Name <span class="text-danger">*</span></label>
                                            <input name="v_name" class="form-control" value="{{ old('v_name') }}"
                                                placeholder="Visitor name" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Phone</label>
                                            <input name="v_phone" class="form-control" value="{{ old('v_phone') }}"
                                                placeholder="Phone">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="v_email" class="form-control"
                                                value="{{ old('v_email') }}" placeholder="Email">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Company</label>
                                            <input type="text" name="v_company" class="form-control"
                                                value="{{ old('v_company') }}" placeholder="Company">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">ID Type</label>
                                            <input name="v_id_type" class="form-control" value="{{ old('v_id_type') }}"
                                                placeholder="Aadhar/PAN/Passport">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">ID Number</label>
                                            <input name="v_id_number" class="form-control" value="{{ old('v_id_number') }}"
                                                placeholder="ID no.">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Vehicle No.</label>
                                            <input name="v_vehicle" class="form-control" value="{{ old('v_vehicle') }}"
                                                placeholder="Optional">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Upload Photo (Fallback)</label>
                                            <input type="file" name="v_photo_file" accept="image/*" capture="user"
                                                class="form-control">
                                            <small class="text-muted">Optional. Used only if you don't capture via
                                                webcam.</small>
                                        </div>
                                        <div class="col-md-4 " style="padding-top:28px;">
                                            <button type="button" class="btn btn-primary vs-next"
                                                data-next="2">Next</button>
                                        </div>
                                    </div>
                                </div>
                                {{-- Webcam / Photo capture --}}
                                <div class="col-md-3">
                                    <label class="form-label">Capture Photo (Webcam)</label>
                                    <div class="border rounded p-2">
                                        <div class="d-flex gap-3 align-items-start">
                                            <video id="cam" autoplay playsinline muted
                                                style="width:100%;height:180px;background:#000;border-radius:6px;"></video>
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

                                {{-- File fallback --}}

                            </div>
                        </div>
                    </div>


                </fieldset>

                {{-- STEP 2: Visit --}}
                <fieldset class="vs-step-pane" data-step="2" aria-labelledby="step2-title">
                    <div class="card mb-3">
                        <div id="step2-title" class="card-header fw-semibold">Visit</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Site <span class="text-danger">*</span></label>
                                    <select name="site_id" class="form-select" required>
                                        <option value="">Select site</option>
                                        @foreach ($sites as $s)
                                            <option value="{{ $s->id }}" @selected(old('site_id') == $s->id)>
                                                {{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Host (Employee) <span class="text-danger">*</span></label>
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
                                    <label class="form-label">Purpose <span class="text-danger">*</span></label>
                                    <input name="purpose" class="form-control" value="{{ old('purpose') }}"
                                        placeholder="Meeting, Interview, Delivery..." required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between gap-2">
                        <button type="button" class="btn btn-light vs-prev" data-prev="1">Back</button>
                        <button type="button" class="btn btn-primary vs-next" data-next="3">Next</button>
                    </div>
                </fieldset>

                {{-- STEP 3: Review & Submit --}}
                <fieldset class="vs-step-pane" data-step="3" aria-labelledby="step3-title">
                    <div id="step3-title" class="card mb-3">
                        <div class="card-header fw-semibold">Review</div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <h6 class="text-uppercase text-muted mb-2">Visitor</h6>
                                    <div class="vs-review" id="reviewVisitor"></div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-uppercase text-muted mb-2">Visit</h6>
                                    <div class="vs-review" id="reviewVisit"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between gap-2">
                        <button type="button" class="btn btn-light vs-prev" data-prev="2">Back</button>
                        <button class="btn btn-success">Create & Send for Approval</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
    <script>
        (() => {
            // --- stepper ---
            const form = document.getElementById('visitWizardForm');
            const panes = Array.from(document.querySelectorAll('.vs-step-pane'));
            const circles = Array.from(document.querySelectorAll('.vs-step .circle'));

            const nextBtns = document.querySelectorAll('.vs-next');
            const prevBtns = document.querySelectorAll('.vs-prev');

            const step = () => Number(document.querySelector('.vs-step-pane.active')?.dataset.step || 1);

            function goToStep(n) {
                panes.forEach(p => p.classList.remove('active'));
                const pane = panes.find(p => Number(p.dataset.step) === n);
                if (pane) {
                    pane.classList.add('active');
                }

                circles.forEach(c => {
                    const s = Number(c.dataset.step);
                    c.classList.remove('active', 'done');
                    if (s < n) c.classList.add('done');
                    if (s === n) c.classList.add('active');
                });

                // On entering step 3, build review
                if (n === 3) buildReview();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            // simple validator for required fields in the current step
            function validateStep(n) {
                const pane = panes.find(p => Number(p.dataset.step) === n);
                if (!pane) return true;
                const required = pane.querySelectorAll('[required]');
                for (const el of required) {
                    if (!el.value || (el.tagName === 'SELECT' && !el.value)) {
                        el.focus();
                        el.classList.add('is-invalid');
                        setTimeout(() => el.classList.remove('is-invalid'), 1500);
                        return false;
                    }
                }
                return true;
            }

            nextBtns.forEach(b => {
                b.addEventListener('click', () => {
                    const target = Number(b.dataset.next);
                    const current = step();
                    if (!validateStep(current)) return;
                    goToStep(target);
                });
            });

            prevBtns.forEach(b => {
                b.addEventListener('click', () => goToStep(Number(b.dataset.prev)));
            });

            // clickable circles (optional jump back)
            circles.forEach(c => {
                c.addEventListener('click', () => {
                    const target = Number(c.dataset.step);
                    const current = step();
                    // allow backward jump freely; forward jump only if current valid
                    if (target <= current || validateStep(current)) goToStep(target);
                });
            });

            // --- review builder ---
            function kv(k, v) {
                const wrap = document.createElement('div');
                wrap.className = 'vs-review-item';
                wrap.innerHTML =
                    `<span class="k">${k}</span><span class="v">${v || '<span class="text-muted">—</span>'}</span>`;
                return wrap;
            }

            function val(name) {
                return (form.querySelector(`[name="${name}"]`) || {}).value || '';
            }

            function optionText(selName) {
                const el = form.querySelector(`[name="${selName}"]`);
                if (!el) return '';
                const opt = el.options[el.selectedIndex];
                return opt ? opt.text : '';
            }

            function buildReview() {
                const rvV = document.getElementById('reviewVisitor');
                const rvT = document.getElementById('reviewVisit');
                rvV.innerHTML = '';
                rvT.innerHTML = '';

                rvV.appendChild(kv('Name', val('v_name')));
                rvV.appendChild(kv('Phone', val('v_phone')));
                rvV.appendChild(kv('Email', val('v_email')));
                rvV.appendChild(kv('Company', val('v_company')));
                rvV.appendChild(kv('ID Type', val('v_id_type')));
                rvV.appendChild(kv('ID Number', val('v_id_number')));
                rvV.appendChild(kv('Vehicle No.', val('v_vehicle')));

                rvT.appendChild(kv('Site', optionText('site_id')));
                rvT.appendChild(kv('Host', optionText('host_user_id')));
                rvT.appendChild(kv('Scheduled Start', val('scheduled_start')));
                rvT.appendChild(kv('Scheduled End', val('scheduled_end')));
                rvT.appendChild(kv('Purpose', val('purpose')));
            }

            // initialize step 1
            goToStep(1);
        })();
    </script>
@endsection
