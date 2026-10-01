@extends('layouts.admin.master')

@section('main-content')

<style>
.form-label { font-weight: 600; }
</style>

<div class="content container-fluid">

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
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

<h3 class="page-title mb-3">
    {{ $steps->count() ? 'Edit Payment Plan Steps' : 'Add Payment Plan Steps' }}
</h3>

<form method="POST" action="{{ route('org.payment-plan.steps.store') }}">
@csrf

<input type="hidden" name="payment_plan_id" value="{{ encrypt($plan->id) }}">

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <strong>Payment Plan Steps</strong>
        <button type="button" class="btn btn-sm btn-primary" id="addStep">+ Add Step</button>
    </div>

    <div class="card-body">
        <table class="table table-bordered align-middle" id="stepsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>%</th>
                    <th>Stage</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Fixed</th>
                    <th>Sort</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

            {{-- EDIT MODE --}}
            @if($steps->count())
                @foreach($steps as $i => $step)
                <tr>
                    <td>
                        <input type="text" name="step_name[]"
                               class="form-control"
                               value="{{ old('step_name.'.$i, $step->name) }}"
                               required>
                    </td>

                    <td>
                        <input type="number" step="0.01" name="percentage[]"
                               class="form-control"
                               value="{{ old('percentage.'.$i, $step->percentage) }}">
                    </td>

                    <td>
                        <select name="stage_type[]" class="form-select">
                            @foreach(['on_booking','during_construction','on_handover','post_handover'] as $st)
                                <option value="{{ $st }}"
                                    {{ old('stage_type.'.$i, $step->stage_type) == $st ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_',' ',$st)) }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    <td><input name="start[]" class="form-control"
                               value="{{ old('start.'.$i, $step->start) }}"></td>

                    <td><input name="end[]" class="form-control"
                               value="{{ old('end.'.$i, $step->end) }}"></td>

                    <td><input name="fixed_amount[]" class="form-control"
                               value="{{ old('fixed_amount.'.$i, $step->fixed_amount) }}"></td>

                    <td><input name="step_sort_order[]" class="form-control"
                               value="{{ old('step_sort_order.'.$i, $step->sort_order) }}"></td>

                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeStep">X</button>
                    </td>
                </tr>
                @endforeach

            {{-- ADD MODE --}}
            @else
                <tr>
                    <td><input name="step_name[]" class="form-control" required></td>
                    <td><input name="percentage[]" class="form-control"></td>
                    <td>
                        <select name="stage_type[]" class="form-select">
                            <option value="on_booking">On Booking</option>
                            <option value="during_construction">During Construction</option>
                            <option value="on_handover">On Handover</option>
                            <option value="post_handover">Post Handover</option>
                        </select>
                    </td>
                    <td><input name="start[]" class="form-control"></td>
                    <td><input name="end[]" class="form-control"></td>
                    <td><input name="fixed_amount[]" class="form-control"></td>
                    <td><input name="step_sort_order[]" class="form-control" value="0"></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeStep">X</button>
                    </td>
                </tr>
            @endif

            </tbody>
        </table>
    </div>

    <div class="text-end p-3">
        <button class="btn btn-success">
            {{ $steps->count() ? 'Update Steps' : 'Save Steps' }}
        </button>
        <a href="{{ route('org.projects.edit', $project->id) }}" class="btn btn-link">
            Cancel
        </a>
    </div>
</div>
</form>

@endsection
@section('scripts')
<script>
$(document).ready(function () {

    $('#addStep').on('click', function () {
        $('#stepsTable tbody').append(`
        <tr>
            <td><input name="step_name[]" class="form-control" required></td>
            <td><input name="percentage[]" class="form-control"></td>
            <td>
                <select name="stage_type[]" class="form-select">
                    <option value="on_booking">On Booking</option>
                    <option value="during_construction">During Construction</option>
                    <option value="on_handover">On Handover</option>
                    <option value="post_handover">Post Handover</option>
                </select>
            </td>
            <td><input name="start[]" class="form-control"></td>
            <td><input name="end[]" class="form-control"></td>
            <td><input name="fixed_amount[]" class="form-control"></td>
            <td><input name="step_sort_order[]" class="form-control" value="0"></td>
            <td><button type="button" class="btn btn-danger btn-sm removeStep">X</button></td>
        </tr>`);
    });

    $(document).on('click', '.removeStep', function () {
        $(this).closest('tr').remove();
    });

});
</script>
@endsection
