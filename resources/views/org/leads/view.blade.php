@extends('layouts.admin.master')

@section('main-content')
    <style>
        .crm-container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        .crm-header {
            background-color: white;
            border-bottom: 1px solid #dee2e6;
            padding: 12px 16px;
            width: 100%;
        }

        .crm-status-btn {
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            border: 1px solid;
        }

        .crm-status-btn-cold {
            border-color: #0dcaf0;
            color: #0dcaf0;
        }

        .crm-status-btn-cold:hover {
            background-color: rgba(13, 202, 240, 0.1);
        }

        .crm-status-btn-warm {
            border-color: #fd7e14;
            color: #fd7e14;
        }

        .crm-status-btn-warm:hover {
            background-color: rgba(253, 126, 20, 0.1);
        }

        .crm-status-btn-hot {
            border-color: #dc3545;
            color: #dc3545;
        }

        .crm-status-btn-hot:hover {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .crm-action-btn {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 14px;
        }

        .crm-action-btn:hover {
            background-color: #e9ecef;
        }

        .crm-text-btn {
            text-decoration: none;
            font-size: 14px;
            border: none;
        }

        .crm-vr {
            height: 24px;
            width: 1px;
            background-color: #dee2e6;
        }

        .crm-tab-container {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 4px;
            background-color: #f8f9fa;
        }

        .crm-tab-button {
            border: none;
            border-radius: 5px;
            padding: 6px 16px;
            font-size: 14px;
            background: transparent;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .crm-tab-button.active {
            background-color: #007AFF;
            color: white;
            box-shadow: 0 2px 4px rgba(0, 122, 255, 0.2);
        }

        .crm-tab-button:hover:not(.active) {
            background-color: rgba(0, 122, 255, 0.1);
            color: #007AFF;
        }

        .crm-tab-content {
            margin-top: 16px;
        }

        .crm-tab-pane {
            display: none;
        }

        .crm-tab-pane.active {
            display: block;
        }

        .crm-card {
            border-radius: 5px;
            /* border-radius: 10px; */
            /* border: 1px solid #dee2e6; */
            background-color: #f5f5f5;
            width: 100%;
            transition: all 0.3s ease;
            margin-bottom: 8px;
        }

        .crm-card:hover {
            transform: translateY(-6px);
            box-shadow: -8px 0 20px rgba(0, 0, 0, 0.12), 8px 0 20px rgba(0, 0, 0, 0.12);
        }

        .crm-bg-custom-light {
            background-color: #f8f9fa;
        }

        .crm-btn-primary-custom {
            background-color: #007AFF;
            border-color: #007AFF;
            color: white;
        }

        .crm-btn-primary-custom:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .crm-form-control-custom,
        .crm-form-select-custom {
            background-color: #f8f9fa;
            border-color: #ced4da;
        }

        .crm-activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .crm-activity-filters {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: nowrap;
        }

        .form-select {
            padding: 6px 12px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 14px;
            background-color: white;
        }

        .crm-activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .crm-activity-item {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .crm-activity-item:last-child {
            border-bottom: none;
        }

        .crm-activity-checkbox {
            margin-top: 2px;
        }

        .crm-activity-content {
            flex: 1;
        }

        .crm-activity-title {
            font-weight: 500;
            margin-bottom: 4px;
            color: #333;
        }

        .crm-activity-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .crm-activity-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #888;
            font-size: 12px;
        }

        .crm-activity-time {
            display: flex;
            align-items: center;
            gap: 4px;
        }



        .crm-show-system-tasks {
            text-align: center;
            padding: 12px;
            color: #007AFF;
            cursor: pointer;
            border-top: 1px solid #f0f0f0;
        }

        .crm-show-system-tasks:hover {
            background-color: #f8f9fa;
        }

        .crm-collapsible-header {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-bottom: 1px solid #dee2e6;
        }

        .crm-collapsible-header:hover {
            background-color: #f8f9fa;
        }

        .crm-collapsible-content {
            padding: 16px;
            border-bottom: 1px solid #dee2e6;
        }

        .crm-collapsible-section {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 16px;
            width: 100%;
        }

        .crm-collapsible-icon {
            transition: transform 0.3s ease;
        }

        .crm-collapsible-icon.rotated {
            transform: rotate(180deg);
        }

        .crm-main-content {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        .crm-row {
            width: 100%;
            margin: 0;
        }

        .crm-col {
            padding-left: 8px;
            padding-right: 8px;
        }

        @media (max-width: 1200px) {

            .crm-header-buttons,
            .crm-status-buttons,
            .crm-action-buttons,
            .crm-activity-filters {
                flex-wrap: wrap;
                gap: 8px;
            }

            .crm-activity-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 768px) {
            .crm-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .crm-header-buttons {
                width: 100%;
                justify-content: flex-start;
            }

            .crm-activity-filters {
                width: 100%;
            }

            .form-select {
                flex: 1;
                min-width: 120px;
            }
        }

        .crm-pin-top {
            background-color: transparent;
            /* border-left: 3px solid #ffc107; */
            padding: 12px 16px;
            border-bottom: lightgray 1px solid;

        }

        .crm-offer-history-item {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .crm-offer-history-item:last-child {
            border-bottom: none;
        }

        .crm-offer-icon {
            background-color: rgba(25, 135, 84, 0.1);
            border-radius: 50%;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .crm-offer-details {
            flex: 1;
        }

        .crm-offer-title {
            font-weight: 500;
            margin-bottom: 4px;
            color: #333;
        }

        .crm-offer-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #888;
            font-size: 12px;
            flex-wrap: wrap;
        }

        .crm-offer-time {
            display: flex;
            align-items: center;
            gap: 4px;
        }
    </style>

    <div class="crm-container">
        <header class="crm-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-success p-0 me-3">
                    <span class="material-symbols-outlined fs-4">edit_square</span>
                </button>
                <h1 class="h4 fw-bold mb-0 me-3">{{ $lead->name }}</h1>
            </div>

            <div class="d-flex align-items-center crm-header-buttons">
                <div class="crm-vr me-2"></div>

                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-success d-flex align-items-center me-3">
                        <span class="material-symbols-outlined me-1">save</span>
                        Save
                    </button>

                    <button class="btn btn-sm btn-danger d-flex align-items-center">
                        <span class="material-symbols-outlined me-1">cancel</span>
                        Cancel
                    </button>
                </div>
            </div>
        </header>

        <div class="crm-main-content overflow-auto p-4">
            <div class="crm-row row g-4">
                <div class="crm-col col-12 col-lg-6">
                    <div class="crm-card p-4 mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-2 bg-danger bg-opacity-10 rounded-circle me-3">
                                <span class="material-symbols-outlined text-danger">person</span>
                            </div>
                            <h3 class="h5 fw-semibold mb-0">Contact</h3>
                        </div>
                        <div class="border rounded p-3 crm-bg-custom-light">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="d-flex align-items-center mb-2">
                                        <p class="fw-semibold text-primary mb-0 me-2">{{ $lead->name }}</p>
                                        <span class="text-muted me-2">|</span>
                                        <p class="text-muted small mb-0">Lead No: {{ $lead->lead_no }}</p>
                                    </div>
                                    <div class="d-flex align-items-center flex-wrap text-muted small">
                                        <div class="d-flex align-items-center me-3">
                                            <span class="material-symbols-outlined me-1">phone</span>
                                            <span>{{ $lead->phone }}</span>
                                        </div>
                                        <span class="text-muted me-3">|</span>
                                        <div class="d-flex align-items-center me-3">
                                            <span class="material-symbols-outlined me-1">mail</span>
                                            <span>{{ $lead->email }}</span>
                                        </div>


                                    </div>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary py-2 px-3 ">Buyer</span>
                            </div>
                        </div>
                    </div>

                    <div class="crm-card p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="p-2 bg-primary bg-opacity-10 rounded-circle me-3">
                                    <span class="material-symbols-outlined text-primary">info</span>
                                </div>
                                <h3 class="h5 fw-semibold mb-0">Lead Details</h3>
                            </div>
                            <button class="btn btn-sm btn-success bg-opacity-10 d-flex align-items-center">View Matching
                                Units</button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="assigned-to" class="form-label text-muted small">Assigned To</label>
                                <select class="form-select crm-form-select-custom" id="assigned-to">
                                    <option class="text-danger"></option>
                                </select>

                            </div>
                            <div class="col-md-6">
                                <label for="referred-by" class="form-label text-muted small">Referred By</label>
                                <input type="text" class="form-control crm-form-control-custom" id="referred-by"
                                    value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Alt Phone</label>
                                <input name="alt_phone" type="number" class="form-control"
                                    value="{{ old('alt_phone', $lead->alt_phone) }}" {{ $disabled }}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Source</label>
                                <input name="source" type="text" class="form-control"
                                    value="{{ old('source', $lead->source) }}" placeholder="Portal / Referral / Walk-in"
                                    {{ $disabled }}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status *</label>
                                <select name="status" class="form-select" required {{ $disabled }}>
                                    @foreach (['new', 'contacted', 'site_visit', 'booked', 'lost'] as $s)
                                        <option value="{{ $s }}" @selected(old('status', $lead->status ?? 'new') == $s)>
                                            {{ ucwords(str_replace('_', ' ', $s)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Follow-up At</label>
                                <input type="datetime-local" name="follow_up_at" class="form-control"
                                    value="{{ old('follow_up_at', $lead->follow_up_at ? $lead->follow_up_at->format('Y-m-d\TH:i') : '') }}"
                                    {{ $disabled }}>
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Min Budget</label>
                                <input type="number" step="0.01" name="budget_min" class="form-control"
                                    value="{{ old('budget_min', $lead->budget_min) }}" {{ $disabled }}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Max Budget</label>
                                <input type="number" step="0.01" name="budget_max" class="form-control"
                                    value="{{ old('budget_max', $lead->budget_max) }}" {{ $disabled }}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Preferred BHK</label>
                                <input name="preferred_bhk" type="text" class="form-control"
                                    value="{{ old('preferred_bhk', $lead->preferred_bhk) }}" {{ $disabled }}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Property Type</label>
                                <input type="text" name="property_type" class="form-control"
                                    value="{{ old('property_type', $lead->property_type) }}" {{ $disabled }}>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Buildup Status</label>
                                <input type="text" name="property_build" class="form-control"
                                    value="{{ old('property_build', $lead->property_build) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Timeframe</label>
                                <input type="text" name="timeframe" class="form-control"
                                    value="{{ old('timeframe', $lead->timeframe) }}" {{ $disabled }}>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Preferred Langugae</label>
                                <input type="text" name="pref_langugae" class="form-control"
                                    value="{{ old('pref_langugae', $lead->pref_langugae) }}" {{ $disabled }}>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="crm-card">
                        <div class="crm-collapsible-section">
                            <div class="crm-collapsible-header" style="cursor: default; background-color: transparent;">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 bg-info bg-opacity-10 rounded-circle me-3">
                                        <span class="material-symbols-outlined text-info">link</span>
                                    </div>
                                    <h3 class="h5 fw-semibold mb-0">UTM Parameters</h3>
                                </div>
                                <span class="material-symbols-outlined crm-collapsible-icon"
                                    style="display: none;">expand_more</span>
                            </div>
                            <div class="crm-collapsible-content" style="display: block;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="utm-id" class="form-label small text-muted">UTM ID</label>
                                        <input type="text" class="form-control crm-form-control-custom" id="utm-id"
                                            placeholder="Enter utm id">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="utm-medium" class="form-label small text-muted">UTM Medium</label>
                                        <input type="text" class="form-control crm-form-control-custom"
                                            id="utm-medium" placeholder="Enter utm medium">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="utm-content" class="form-label small text-muted">UTM Content</label>
                                        <input type="text" class="form-control crm-form-control-custom"
                                            id="utm-content" placeholder="Enter utm content">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="utm-ad-set" class="form-label small text-muted">UTM Ad Set</label>
                                        <input type="text" class="form-control crm-form-control-custom"
                                            id="utm-ad-set" placeholder="Enter ad set">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="utm-source" class="form-label small text-muted">UTM Source</label>
                                        <input type="text" class="form-control crm-form-control-custom"
                                            id="utm-source" placeholder="Enter utm source">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="utm-term" class="form-label small text-muted">UTM Term</label>
                                        <input type="text" class="form-control crm-form-control-custom" id="utm-term"
                                            placeholder="Enter utm term">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="utm-campaign" class="form-label small text-muted">UTM Campaign</label>
                                        <input type="text" class="form-control crm-form-control-custom" id="utm-term"
                                            placeholder="Enter utm campaign">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>

                <div class="crm-col col-12 col-lg-6">
                    {{-- <div class="crm-card ">
                        <div class="crm-collapsible-section">
                            <div class="crm-collapsible-header" data-target="tasks-content">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 bg-warning bg-opacity-10 rounded-circle me-3">
                                        <span class="material-symbols-outlined text-warning">list_alt</span>
                                    </div>
                                    <h3 class="h5 fw-semibold mb-0">Tasks</h3>
                                </div>
                                <span class="material-symbols-outlined crm-collapsible-icon">expand_more</span>
                            </div>
                            <div class="crm-collapsible-content" id="tasks-content" data-lead-id="{{ $lead->id }}"
                                data-tasks-index-url="{{ route('leads.tasks.index', $lead) }}"
                                data-tasks-store-url="{{ route('leads.tasks.store', $lead) }}">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="crm-tab-container">
                                        <button type="button" class="crm-tab-button active"
                                            data-tab="notes">Notes</button>
                                        <button type="button" class="crm-tab-button" data-tab="viewing">Viewing</button>
                                        <button type="button" class="crm-tab-button" data-tab="call">Call</button>
                                    </div>
                                    <button type="button" id="lead-task-save-btn"
                                        class="btn crm-btn-primary-custom btn-sm">
                                        Save
                                    </button>
                                </div>

                                <div class="crm-tab-content">
                                    <div class="crm-tab-pane active" id="notes-tab" data-task-type="note">
                                        <textarea class="form-control crm-form-control-custom" rows="4" name="note" id="task-note"
                                            placeholder="Start typing to leave a note or @mention someone to notify them..."></textarea>
                                    </div>

                                    <div class="crm-tab-pane" id="viewing-tab" data-task-type="viewing">
                                        <div class="mb-3">
                                            <label for="unit-viewing" class="form-label small text-muted">Unit</label>
                                            <select class="form-select crm-form-select-custom" id="unit-viewing"
                                                name="unit_ref">
                                                <option value="">Enter unit no or unit ref no or property</option>
                                            </select>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="mark-completed-viewing" name="is_completed">
                                                    <label class="form-check-label small" for="mark-completed-viewing">
                                                        Mark As Completed
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-9">
                                                <div class="d-flex align-items-center">
                                                    <label class="form-label small me-2 mb-0" for="follow-up-viewing">
                                                        Schedule Follow-Up
                                                    </label>
                                                    <input type="text"
                                                        class="form-control crm-form-control-custom w-auto text-center"
                                                        id="follow-up-viewing" name="schedule_follow_up_days">
                                                    <span class="ms-2 small text-muted">Days</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <label for="date-viewing" class="form-label small text-muted">Date</label>
                                                <div class="input-group">
                                                    <input type="date" class="form-control crm-form-control-custom"
                                                        id="date-viewing" name="date">
                                                    <span class="input-group-text bg-transparent border-start-0">
                                                        <span
                                                            class="material-symbols-outlined text-muted">calendar_today</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label for="time-viewing" class="form-label small text-muted">Time</label>
                                                <div class="input-group">
                                                    <input type="time" class="form-control crm-form-control-custom"
                                                        id="time-viewing" name="time">
                                                    <span class="input-group-text bg-transparent border-start-0">
                                                        <span class="material-symbols-outlined text-muted">schedule</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="user-viewing" class="form-label small text-muted">User</label>
                                            <input type="text" class="form-control crm-form-control-custom"
                                                id="user-viewing" name="user_name"
                                                placeholder="Assign to user (optional)">
                                        </div>

                                        <div>
                                            <h5 class="small fw-semibold mb-2">Reminders</h5>
                                            <div class="d-flex align-items-center mb-2">
                                                <select class="form-select crm-form-select-custom me-2">
                                                    <option>Email</option>
                                                </select>
                                                <select class="form-select crm-form-select-custom me-2">
                                                    <option>Agent</option>
                                                </select>
                                                <input type="number"
                                                    class="form-control crm-form-control-custom text-center me-2"
                                                    value="15">
                                                <select class="form-select crm-form-select-custom me-2">
                                                    <option>Mins Before</option>
                                                </select>
                                                <button class="btn btn-link text-danger p-0" type="button">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </div>
                                            <button class="btn btn-link text-primary p-0 small text-decoration-none"
                                                type="button">
                                                Add a reminder
                                            </button>
                                        </div>
                                    </div>

                                    <div class="crm-tab-pane" id="call-tab" data-task-type="call">
                                        <div class="row mb-3">
                                            <div class="col-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="mark-completed-call" name="is_completed_call">
                                                    <label class="form-check-label small" for="mark-completed-call">
                                                        Mark As Completed
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-9">
                                                <div class="d-flex align-items-center">
                                                    <label class="form-label small me-2 mb-0" for="follow-up-call">
                                                        Schedule Follow-Up
                                                    </label>
                                                    <input type="text"
                                                        class="form-control crm-form-control-custom w-auto text-center"
                                                        id="follow-up-call" name="schedule_follow_up_days_call">
                                                    <span class="ms-2 small text-muted">Days</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <label for="date-call" class="form-label small text-muted">Date</label>
                                                <div class="input-group">
                                                    <input type="date" class="form-control crm-form-control-custom"
                                                        id="date-call" name="date_call">
                                                    <span class="input-group-text bg-transparent border-start-0">
                                                        <span
                                                            class="material-symbols-outlined text-muted">calendar_today</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label for="time-call" class="form-label small text-muted">Time</label>
                                                <div class="input-group">
                                                    <input type="time" class="form-control crm-form-control-custom"
                                                        id="time-call" name="time_call">
                                                    <span class="input-group-text bg-transparent border-start-0">
                                                        <span class="material-symbols-outlined text-muted">schedule</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="user-call" class="form-label small text-muted">User</label>
                                            <input type="text" class="form-control crm-form-control-custom"
                                                id="user-call" name="user_name_call"
                                                placeholder="Assign to user (optional)">
                                        </div>

                                        <div>
                                            <h5 class="small fw-semibold mb-2">Reminders</h5>
                                            <div class="d-flex align-items-center mb-2">
                                                <select class="form-select crm-form-select-custom me-2">
                                                    <option>Email</option>
                                                </select>
                                                <select class="form-select crm-form-select-custom me-2">
                                                    <option>Agent</option>
                                                </select>
                                                <input type="number"
                                                    class="form-control crm-form-control-custom text-center me-2"
                                                    value="15">
                                                <select class="form-select crm-form-select-custom me-2">
                                                    <option>Mins Before</option>
                                                </select>
                                                <button class="btn btn-link text-danger p-0" type="button">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </div>
                                            <button class="btn btn-link text-primary p-0 small text-decoration-none"
                                                type="button">
                                                Add a reminder
                                            </button>
                                        </div>
                                    </div>


                                </div>



                            </div>
                        </div>
                    </div> --}}
                    <div class="crm-card ">
                        <div class="crm-collapsible-section">
                            <div class="crm-collapsible-header" data-target="tasks-content">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 bg-warning bg-opacity-10 rounded-circle me-3">
                                        <span class="material-symbols-outlined text-warning">list_alt</span>
                                    </div>
                                    <h3 class="h5 fw-semibold mb-0">Tasks</h3>
                                </div>
                                <span class="material-symbols-outlined crm-collapsible-icon">expand_more</span>
                            </div>
                            <div class="crm-collapsible-content" id="tasks-content" data-lead-id="{{ $lead->id }}"
                                data-tasks-index-url="{{ route('leads.tasks.index', $lead) }}"
                                data-tasks-store-url="{{ route('leads.tasks.store', $lead) }}">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="crm-tab-container">
                                        <button type="button" class="crm-tab-button active"
                                            data-tab="notes">Notes</button>
                                        <button type="button" class="crm-tab-button" data-tab="viewing">Viewing</button>
                                        <button type="button" class="crm-tab-button" data-tab="call">Call</button>
                                    </div>
                                    <button type="button" id="lead-task-save-btn"
                                        class="btn btn-sm btn-success d-flex align-items-center">                                        <span class="material-symbols-outlined me-1">save</span>

                                        Save
                                    </button>
                                </div>

                                {{-- FORM AREA --}}
                                <div class="crm-tab-content">
                                    {{-- Common Textarea for all tabs --}}
                                    <div class="mb-3">
                                        <textarea class="form-control crm-form-control-custom" rows="4" name="note" id="task-note"
                                            placeholder="Start typing to leave a note or @mention someone to notify them..."></textarea>
                                    </div>

                                    {{-- NOTES TAB --}}
                                    <div class="crm-tab-pane active" id="notes-tab" data-task-type="note">
                                        {{-- Only additional fields for notes if any --}}
                                    </div>

                                    {{-- VIEWING TAB --}}
                                    <div class="crm-tab-pane" id="viewing-tab" data-task-type="viewing">
                                        <div class="mb-3">
                                            <label for="unit-viewing" class="form-label small text-muted">Unit</label>
                                            {{-- <select class="form-select crm-form-select-custom" id="unit-viewing"
                                                name="unit_ref">
                                                <option value="">Enter unit no or unit ref no or property</option>
                                            </select> --}}
                                            <select class="form-select crm-form-select-custom" id="unit-viewing"
                                                name="unit_ref">
                                                <option value="">Select Unit</option>
                                                @foreach ($unit as $u)
                                                    <option value="{{ $u->id }}">{{ $u->unit_number }} -
                                                        {{ $u->property_name ?? 'N/A' }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="mark-completed-viewing" name="is_completed">
                                                    <label class="form-check-label small" for="mark-completed-viewing">
                                                        Mark As Completed
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-9">
                                                <div class="d-flex align-items-center">
                                                    <label class="form-label small me-2 mb-0" for="follow-up-viewing">
                                                        Schedule Follow-Up
                                                    </label>
                                                    <input type="text"
                                                        class="form-control crm-form-control-custom w-auto text-center"
                                                        id="follow-up-viewing" name="schedule_follow_up_days">
                                                    <span class="ms-2 small text-muted">Days</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <label for="date-viewing" class="form-label small text-muted">Date</label>
                                                <div class="input-group">
                                                    <input type="date" class="form-control crm-form-control-custom"
                                                        id="date-viewing" name="date">
                                                    <span class="input-group-text bg-transparent border-start-0">
                                                        <span
                                                            class="material-symbols-outlined text-muted">calendar_today</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label for="time-viewing" class="form-label small text-muted">Time</label>
                                                <div class="input-group">
                                                    <input type="time" class="form-control crm-form-control-custom"
                                                        id="time-viewing" name="time">
                                                    <span class="input-group-text bg-transparent border-start-0">
                                                        <span class="material-symbols-outlined text-muted">schedule</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="user-viewing" class="form-label small text-muted">User</label>
                                            <input type="text" class="form-control crm-form-control-custom"
                                                id="user-viewing" name="user_name"
                                                placeholder="Assign to user (optional)">
                                        </div>

                                        {{-- Reminder UI just visual; not mapped to DB in this example --}}
                                        <div>
                                            <h5 class="small fw-semibold mb-2">Reminders</h5>
                                            <div class="d-flex align-items-center mb-2">
                                                <select class="form-select crm-form-select-custom me-2">
                                                    <option>Email</option>
                                                </select>
                                                <select class="form-select crm-form-select-custom me-2">
                                                    <option>Agent</option>
                                                </select>
                                                <input type="number"
                                                    class="form-control crm-form-control-custom text-center me-1"
                                                    value="15">
                                                <select class="form-select crm-form-select-custom me-2">
                                                    <option>Mins Before</option>
                                                </select>
                                                <button class="btn btn-link text-danger p-0" type="button">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </div>
                                            <button class="btn btn-link text-primary p-0 small text-decoration-none"
                                                type="button">
                                                Add a reminder
                                            </button>
                                        </div>
                                    </div>

                                    {{-- CALL TAB --}}
                                    <div class="crm-tab-pane" id="call-tab" data-task-type="call">
                                        <div class="row mb-3">
                                            <div class="col-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="mark-completed-call" name="is_completed_call">
                                                    <label class="form-check-label small" for="mark-completed-call">
                                                        Mark As Completed
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-9">
                                                <div class="d-flex align-items-center">
                                                    <label class="form-label small me-2 mb-0" for="follow-up-call">
                                                        Schedule Follow-Up
                                                    </label>
                                                    <input type="text"
                                                        class="form-control crm-form-control-custom w-auto text-center"
                                                        id="follow-up-call" name="schedule_follow_up_days_call">
                                                    <span class="ms-2 small text-muted">Days</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <label for="date-call" class="form-label small text-muted">Date</label>
                                                <div class="input-group">
                                                    <input type="date" class="form-control crm-form-control-custom"
                                                        id="date-call" name="date_call">
                                                    <span class="input-group-text bg-transparent border-start-0">
                                                        <span
                                                            class="material-symbols-outlined text-muted">calendar_today</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label for="time-call" class="form-label small text-muted">Time</label>
                                                <div class="input-group">
                                                    <input type="time" class="form-control crm-form-control-custom"
                                                        id="time-call" name="time_call">
                                                    <span class="input-group-text bg-transparent border-start-0">
                                                        <span class="material-symbols-outlined text-muted">schedule</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="user-call" class="form-label small text-muted">User</label>
                                            <input type="text" class="form-control crm-form-control-custom"
                                                id="user-call" name="user_name_call"
                                                placeholder="Assign to user (optional)">
                                        </div>

                                        <div>
                                            <h5 class="small fw-semibold mb-2">Reminders</h5>
                                            <div class="d-flex align-items-center mb-2">
                                                <select class="form-select crm-form-select-custom me-2">
                                                    <option>Email</option>
                                                </select>
                                                <select class="form-select crm-form-select-custom me-2">
                                                    <option>Agent</option>
                                                </select>
                                                <input type="number"
                                                    class="form-control crm-form-control-custom text-center me-2"
                                                    value="15">
                                                <select class="form-select crm-form-select-custom me-2">
                                                    <option>Mins Before</option>
                                                </select>
                                                <button class="btn btn-link text-danger p-0" type="button">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </div>
                                            <button class="btn btn-link text-primary p-0 small text-decoration-none"
                                                type="button">
                                                Add a reminder
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="crm-card">
                        <div class="crm-collapsible-section">
                            <div class="crm-collapsible-header" data-target="activity-content">
                                <h3 class="h5 fw-semibold mb-0"><i class="fas fa-stream blue"></i>Activity History</h3>
                                <span class="material-symbols-outlined crm-collapsible-icon">expand_more</span>
                            </div>
                            <div class="crm-collapsible-content" id="activity-content">
                                <div class="crm-activity-header">
                                    <div class="crm-activity-filters">
                                        <select class="form-select">
                                            <option>Everyone</option>
                                            <option>Me</option>
                                            <option>Team</option>
                                        </select>
                                        <select class="form-select">
                                            <option>Types</option>
                                            <option>Call</option>
                                            <option>Note</option>
                                            <option>Viewing</option>
                                        </select>
                                        <select class="form-select">
                                            <option>Status</option>
                                            <option>Completed</option>
                                            <option>Pending</option>
                                        </select>
                                        <select class="form-select">
                                            <option>Lead</option>
                                            <option>Hot</option>
                                            <option>Warm</option>
                                            <option>Cold</option>
                                        </select>
                                        <select class="form-select">
                                            <option>Sort By</option>
                                            <option>Newest First</option>
                                            <option>Oldest First</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small fw-medium">Pin to top</span>
                                        <span class="small text-muted">Complete</span>
                                    </div>
                                </div>
                                {{-- <div class="crm-pin-top mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex align-items-start">
                                            <button class="btn btn-success btn-sm p-1 me-2">
                                                <span class="material-symbols-outlined"
                                                    style="font-size: 16px;">save</span>
                                            </button>
                                            <div>
                                                <div class="fw-medium small">Assigned To : Anita Siketa | Log a Call</div>
                                                <div class="text-muted small mt-1">hello</div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <div class="text-end me-2">
                                                <div class="small">14 Nov 2025</div>
                                                <div class="text-muted small mt-1">19:00</div>
                                            </div>
                                            <input class="form-check-input mt-1" type="checkbox">
                                        </div>
                                    </div>
                                </div>
                                <div class="crm-pin-top mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex align-items-start">
                                            <button class="btn btn-success btn-sm p-1 me-2">
                                                <span class="material-symbols-outlined"
                                                    style="font-size: 16px;">save</span>
                                            </button>
                                            <div>
                                                <div class="fw-medium small">Assigned To : Anita Siketa | Log a Call</div>
                                                <div class="text-muted small mt-1">hello</div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <div class="text-end me-2">
                                                <div class="small">14 Nov 2025</div>
                                                <div class="text-muted small mt-1">19:00</div>
                                            </div>
                                            <input class="form-check-input mt-1" type="checkbox">
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="mb-3">

                                    <ul class="crm-activity-list">

                                    </ul>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" name="" id="" class="me-2">
                                        <span class="text-muted">Show system generated tasks</span>

                                    </div>


                                </div>
                            </div>
                        </div>
                        <!-- Offers Section -->

                    </div>
                    <!-- Offers Section -->
                    <div class="crm-card">
                        <div class="crm-collapsible-section">
                            <div class="crm-collapsible-header" data-target="offers-content">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 bg-success bg-opacity-10 rounded-circle me-3">
                                        <span class="material-symbols-outlined text-success">local_offer</span>
                                    </div>
                                    <h3 class="h5 fw-semibold mb-0">Offers</h3>
                                </div>
                                <span class="material-symbols-outlined crm-collapsible-icon">expand_more</span>
                            </div>
                            <div class="crm-collapsible-content" id="offers-content" data-lead-id="{{ $lead->id }}"
                                data-offers-index-url="{{ route('leads.offers.index', $lead) }}"
                                data-offers-store-url="{{ route('leads.offers.store', $lead) }}">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <button class="btn btn-sm btn-primary d-flex align-items-center" id="add-offer-btn">
                                        <span class="material-symbols-outlined me-1">Add</span>
                                      Add Offer
                                    </button>
                                    <button class="btn btn-sm btn-success d-flex align-items-center" id="save-offer-btn"
                                        style="display: none;">
                                        <span class="material-symbols-outlined me-1">save</span>
                                         Save
                                    </button>
                                </div>

                                <div id="offer-form" style="display: none;" class="crm-offer-form mb-4">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label for="unit_id" class="form-label small text-muted">Unit *</label>
                                            <select class="form-select crm-form-select-custom" id="unit_id"
                                                name="unit_id" required>
                                                <option value="">Select Unit</option>
                                                @foreach ($unit as $u)
                                                    <option value="{{ $u->id }}">{{ $u->unit_number }} -
                                                        {{ $u->property_name ?? 'N/A' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="offered_amount" class="form-label small text-muted">Offered Amount
                                                *</label>
                                            <input type="number" class="form-control crm-form-control-custom"
                                                id="offered_amount" name="offered_amount" placeholder="Enter amount"
                                                step="0.01" required>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label small text-muted">Offer Type</label>
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="offer_type"
                                                        id="offer_accepted" value="accepted">
                                                    <label class="form-check-label small"
                                                        for="offer_accepted">Accepted</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="offer_type"
                                                        id="offer_rejected" value="rejected">
                                                    <label class="form-check-label small"
                                                        for="offer_rejected">Rejected</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="status" class="form-label small text-muted">Status *</label>
                                            <select class="form-select crm-form-select-custom" id="status_"
                                                name="status" required>
                                                <option value="pending">Pending</option>
                                                <option value="accepted">Accepted</option>
                                                <option value="rejected">Rejected</option>
                                                <option value="closed">Closed</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="reason" class="form-label small text-muted">Reason or
                                                Comment</label>
                                            <textarea class="form-control crm-form-control-custom" id="reason" name="reason" rows="3"
                                                placeholder="Enter reason or comment"></textarea>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                    <!-- Offer History Section -->
                    <div class="crm-card">
                        <div class="crm-collapsible-section">
                            <div class="crm-collapsible-header" data-target="offer-history-content">
                                <h3 class="h5 fw-semibold mb-0">Offer History</h3>
                                <span class="material-symbols-outlined crm-collapsible-icon">expand_more</span>
                            </div>
                            <div class="crm-collapsible-content" id="offer-history-content">
                                <div class="mb-3">
                                    <ul class="crm-activity-list" id="offer-history-list">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

   <script>
    (function($) {
        function initializeCollapsible() {
            $('.crm-collapsible-header').on('click', function() {
                const target = $(this).data('target');
                const $content = $('#' + target);
                const $icon = $(this).find('.crm-collapsible-icon');

                $content.slideToggle(300);
                $icon.toggleClass('rotated');
            });
        }

        function formatOfferDateTime(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${day} ${month} ${year} ${hours}:${minutes}`;
        }

        function getStatusBadgeClass(status) {
            switch (status) {
                case 'pending': return 'bg-warning';
                case 'accepted': return 'bg-success';
                case 'rejected': return 'bg-danger';
                case 'closed': return 'bg-secondary';
                default: return 'bg-warning';
            }
        }

        function getStatusText(status) {
            switch (status) {
                case 'pending': return 'Pending';
                case 'accepted': return 'Accepted';
                case 'rejected': return 'Rejected';
                case 'closed': return 'Closed';
                default: return status;
            }
        }

        function getOfferTypeBadgeClass(type) {
            switch (type) {
                case 'accepted': return 'bg-success';
                case 'rejected': return 'bg-danger';
                default: return 'bg-secondary';
            }
        }

        $(document).ready(function() {
            const $tasksContent = $('#tasks-content');
            const leadId = $tasksContent.data('lead-id');
            const indexUrl = $tasksContent.data('tasks-index-url');
            const storeUrl = $tasksContent.data('tasks-store-url');

            const $offersContent = $('#offers-content');
            const offersIndexUrl = $offersContent.data('offers-index-url');
            const offersStoreUrl = $offersContent.data('offers-store-url');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            initializeCollapsible();

            $('.crm-tab-button').on('click', function() {
                const tab = $(this).data('tab');

                $('.crm-tab-button').removeClass('active');
                $(this).addClass('active');

                $('.crm-tab-pane').removeClass('active');
                $('#' + tab + '-tab').addClass('active');
            });

            function formatTaskDateTime(dateStr, timeStr) {
                if (!dateStr && !timeStr) return '';

                let dtString = (dateStr || '').trim();
                if (timeStr) {
                    dtString += ' ' + timeStr.trim();
                }

                const d = new Date(dtString.replace(' ', 'T'));

                if (isNaN(d.getTime())) {
                    return (dateStr || '') + (timeStr ? ' ' + timeStr : '');
                }

                const day = String(d.getDate()).padStart(2, '0');
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const month = monthNames[d.getMonth()];
                const year = d.getFullYear();

                const hours = String(d.getHours()).padStart(2, '0');
                const minutes = String(d.getMinutes()).padStart(2, '0');

                return `${day} ${month} ${year} ${hours}:${minutes}`;
            }

            function fetchTasks() {
                const $activityList = $('.crm-activity-list');

                $.get(indexUrl, function(res) {
                    if (!res.success || !res.data || res.data.length === 0) {
                        $activityList.html('');
                        return;
                    }

                    let html = '';
                    console.log(res.data);
                    res.data.forEach(function(task) {
                        let type = (task.task_type || '').toLowerCase();
                        let typeLabel = 'Activity';
                        if (type === 'call') typeLabel = 'Log a Call';
                        else if (type === 'note') typeLabel = 'Notes';
                        else if (type === 'viewing') typeLabel = 'Viewing';

                        let assignedTo = task.user_name || 'Unassigned';

                        let title = `${typeLabel}:`;
                        if (task.unit_number || task.property_name  ) {
                            title += ` | ${task.unit_number ?? ''}${task.property_name ?? ''} `;
                        }

                        if (task.note) {
                            title += ` ${task.note}`;
                        }

                        let timeText = formatTaskDateTime(task.date, task.time);

                        html += `
                            <li class="crm-activity-item">
                                <input type="checkbox" class="crm-activity-checkbox" ${task.is_completed ? 'checked' : ''}>
                                <div class="crm-activity-content">
                                    <div class="crm-activity-title">${title}</div>
                                    <div class="crm-activity-meta">
                                        <span class="crm-activity-time">
                                            <span class="material-symbols-outlined" style="font-size: 14px;">schedule</span>
                                            ${timeText}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        `;
                    });

                    $activityList.html(html);
                }).fail(function() {
                    $activityList.html('<li class="text-danger px-2" style="list-style:none;">Failed to load tasks.</li>');
                });
            }

            fetchTasks();

            $('#lead-task-save-btn').on('click', function() {
                const $activePane = $('.crm-tab-pane.active');
                const taskType = $activePane.data('task-type');

                let payload = {
                    lead_id: leadId,
                    task_type: taskType
                };

                if (taskType === 'note') {
                    payload.note = $('#task-note').val();
                }

                if (taskType === 'viewing') {
                    payload.note = $('#task-note').val() || null;
                    payload.unit_ref = $('#unit-viewing').val();
                    payload.is_completed = $('#mark-completed-viewing').is(':checked') ? 1 : 0;
                    payload.schedule_follow_up_days = $('#follow-up-viewing').val() || null;
                    payload.date = $('#date-viewing').val() || null;
                    payload.time = $('#time-viewing').val() || null;
                }

                if (taskType === 'call') {
                    payload.is_completed = $('#mark-completed-call').is(':checked') ? 1 : 0;
                    payload.schedule_follow_up_days = $('#follow-up-call').val() || null;
                    payload.date = $('#date-call').val() || null;
                    payload.time = $('#time-call').val() || null;
                }

                $.get(storeUrl, payload, function(res) {
                    if (res.success) {
                        fetchTasks();
                        alert('Task saved');
                        if (taskType === 'note') {
                            $('#task-note').val('');
                        }
                    } else {
                        alert(res.message || 'Failed to save task');
                    }
                }).fail(function(xhr) {
                    console.error(xhr.responseText);
                    alert('Error saving task' + xhr.responseText);
                });
            });

            function fetchOffers() {
                $.get(offersIndexUrl, function(response) {
                    if (response.success && response.data.length > 0) {
                        renderOffersList(response.data);
                    }
                }).fail(function() {
                    $('#offers-list').html('<div class="text-danger text-center py-3">Failed to load offers.</div>');
                });
            }

            function fetchOfferHistory() {
                $.get(offersIndexUrl, function(response) {
                    if (response.success && response.data.length > 0) {
                        renderOfferHistory(response.data);
                    } else {
                        $('#offer-history-list').html('<li class="text-muted text-center py-3">No offer history available.</li>');
                    }
                }).fail(function() {
                    $('#offer-history-list').html('<li class="text-danger text-center py-3">Failed to load offer history.</li>');
                });
            }

            function renderOffersList(offers) {
                let html = '';


            }

            function renderOfferHistory(offers) {
                let html = '';

                offers.forEach(function(offer) {
                    html += `
                        <li class="crm-activity-item">
                            <div class="crm-activity-content">
                                <div class="d-flex align-items-start">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2">
                                        <span class="material-symbols-outlined text-success" style="font-size: 16px;">local_offer</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="crm-activity-title">Unit ID: ${offer.unit_id} | Amount: ₹${parseFloat(offer.offered_amount).toLocaleString()}</div>
                                        <div class="crm-activity-description">
                                            Status: <span class="badge ${getStatusBadgeClass(offer.status)}">${getStatusText(offer.status)}</span>
                                            ${offer.offer_type ? ` | Type: <span class="badge ${getOfferTypeBadgeClass(offer.offer_type)}">${offer.offer_type}</span>` : ''}
                                        </div>
                                        <div class="crm-activity-meta">
                                            <span class="crm-activity-time">
                                                <span class="material-symbols-outlined" style="font-size: 14px;">schedule</span>
                                                ${formatOfferDateTime(offer.created_at)}
                                            </span>
                                            <span class="text-muted">|</span>
                                            ${offer.reason ? `<span class="text-muted">Reason: ${offer.reason}</span>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    `;
                });

                $('#offer-history-list').html(html);
            }

            function resetOfferForm() {
                $('#offer-form').slideUp(300);
                $('#save-offer-btn').hide();
                $('#add-offer-btn').show();
                $('#unit_id').val('');
                $('#offered_amount').val('');
                $('#status_').val('pending');
                $('#reason').val('');
                $('input[name="offer_type"]').prop('checked', false);
            }

            fetchOffers();
            fetchOfferHistory();

            $('#add-offer-btn').on('click', function() {
                $('#offer-form').slideDown(300);
                $('#save-offer-btn').show();
                $(this).hide();
            });

            $('#save-offer-btn').on('click', function() {
                const formData = {
                    unit_id: $('#unit_id').val(),
                    offered_amount: $('#offered_amount').val(),
                    status: $('#status_').val(),
                    offer_type: $('input[name="offer_type"]:checked').val(),
                    reason: $('#reason').val()
                };
                    console.log(formData);

                if (!formData.unit_id || !formData.offered_amount || !formData.status) {
                    alert('Please fill in all required fields (Unit, Offered Amount, Status)');
                    return;
                }

                const url = offersStoreUrl + '?' + $.param(formData);

                $.get(url, function(response) {
                    if (response.success) {
                        alert('Offer saved successfully!');
                        resetOfferForm();
                        fetchOffers();
                        fetchOfferHistory();
                    } else {
                        alert('Failed to save offer: ' + response.message);
                    }
                }).fail(function(xhr) {
                    console.error(xhr.responseText);
                    alert('Error saving offer');
                });
            });
        });

    })(jQuery);
</script>
@endsection
