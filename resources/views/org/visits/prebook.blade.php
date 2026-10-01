@extends('layouts.admin.master')
@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Pre-Appointment (Invite a Visitor)</h4>
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

                        <form method="post" action="{{ route('org.visits.prebook.store') }}">
                            @csrf
                            <div class="card mb-3">
                                <div class="card-header fw-semibold">Visitor</div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Name</label>
                                            <input name="v_name" class="form-control" value="{{ old('v_name') }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Email (recommended)</label>
                                            <input type="email" name="v_email" class="form-control"
                                                value="{{ old('v_email') }}">
                                            <small class="text-muted">If provided, we email the pass + calendar
                                                invite.</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Phone (optional)</label>
                                            <input name="v_phone" class="form-control" value="{{ old('v_phone') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header fw-semibold">Visit</div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Site</label>
                                            <select name="site_id" class="form-select" required>
                                                @foreach ($sites as $s)
                                                    <option value="{{ $s->id }}" @selected(old('site_id') == $s->id)>
                                                        {{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Purpose</label>
                                            <input name="purpose" class="form-control" value="{{ old('purpose') }}"
                                                placeholder="Meeting, Interview...">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Start</label>
                                            <input type="datetime-local" name="scheduled_start" class="form-control"
                                                value="{{ old('scheduled_start') }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">End (optional)</label>
                                            <input type="datetime-local" name="scheduled_end" class="form-control"
                                                value="{{ old('scheduled_end') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-primary">Create & Send Invite</button>
                                <a href="{{ route('org.visits.index') }}" class="btn btn-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
       
    @endsection
