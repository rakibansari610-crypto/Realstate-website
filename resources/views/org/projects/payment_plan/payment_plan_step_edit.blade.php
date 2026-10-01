@extends('layouts.admin.master')

@section('main-content')

<style>
    .form-label { font-weight: 600; }
    .table td, .table th { vertical-align: middle; }
</style>

<div class="content container-fluid">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h3 class="page-title mb-3 d-flex">
        Edit Payment Plan Steps  &nbsp; &nbsp;
        <small class="text-muted d-block">
            Plan: {{ $plan->name }}
        </small>
    </h3>

    <form method="POST"
      action="{{ route('org.payment-plan.steps.update', encrypt($plan->id)) }}">

        @csrf 

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <strong>Steps</strong>
            </div>

            <div class="card-body p-0">
                <table class="table table-bordered mb-0" id="stepsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>%</th>
                            <th>Stage</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Fixed Amount</th>
                            <th>Sort</th> 
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <input type="hidden"
                                       name="step_id"
                                       value="{{ $steps->id }}">

                                <input type="text"
                                       name="step_name"
                                       class="form-control"
                                       value="{{ old('step_name', $steps->name) }}"
                                       required>
                            </td>

                            <td>
                                <input type="number"
                                       step="0.01"
                                       name="percentage"
                                       class="form-control"
                                       value="{{ old('percentage', $steps->percentage) }}">
                            </td>

                            <td>
                                <select name="stage_type" class="form-select">
                                    <option value="on_booking"
                                        {{ old('stage_type', $steps->stage_type) == 'on_booking' ? 'selected' : '' }}>
                                        On Booking
                                    </option>

                                    <option value="during_construction"
                                        {{ old('stage_type', $steps->stage_type) == 'during_construction' ? 'selected' : '' }}>
                                        During Construction
                                    </option>

                                    <option value="on_handover"
                                        {{ old('stage_type', $steps->stage_type) == 'on_handover' ? 'selected' : '' }}>
                                        On Handover
                                    </option>

                                    <option value="post_handover"
                                        {{ old('stage_type', $steps->stage_type) == 'post_handover' ? 'selected' : '' }}>
                                        Post Handover
                                    </option>
                                </select>
                            </td>

                            <td>
                                <input type="number"
                                       name="start"
                                       class="form-control"
                                       value="{{ old('start', $steps->start) }}">
                            </td>

                            <td>
                                <input type="number"
                                       name="end"
                                       class="form-control"
                                       value="{{ old('end', $steps->end) }}">
                            </td>

                            <td>
                                <input type="number"
                                       name="fixed_amount"
                                       class="form-control"
                                       value="{{ old('fixed_amount', $steps->fixed_amount) }}">
                            </td>

                            <td>
                                <input type="number"
                                       name="step_sort_order"
                                       class="form-control"
                                       value="{{ old('step_sort_order', $steps->sort_order) }}">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-success">
                    Update Steps
                </button>

                <a href="{{ route('org.projects.edit', $project->id) }}"
                   class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
   
</script>
@endsection
