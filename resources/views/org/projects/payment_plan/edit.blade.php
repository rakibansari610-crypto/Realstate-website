@extends('layouts.admin.master')

@section('main-content')

<style>
.form-label { font-weight: 600; }
.preview-box {
    border: 1px dashed #dcdcdc;
    padding: 10px;
    border-radius: 6px;
    background: #fafafa;
    text-align: center;
}
</style>

<div class="content container-fluid">

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

<h3 class="page-title mb-3">Edit Payment Plan</h3>
    <form method="POST" action="{{ route('org.payment-plan.store') }}">
    @csrf

    <input type="hidden" name="project_id" value="{{ $project->id }}">

    {{-- EDIT case --}}
    @isset($plan)
        <input type="hidden" name="payment_plan_id" value="{{ encrypt($plan->id) }}">
    @endisset

    {{-- ================= PARENT PLAN ================= --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>Payment Plan</strong>
        </div>

        <div class="card-body row g-3">

            {{-- Plan Name --}}
            <div class="col-md-4">
                <label class="form-label">Plan Name</label>
                <input type="text"
                       name="plan_name"
                       class="form-control"
                       value="{{ old('plan_name', $plan->name ?? '') }}"
                       required>
            </div>

            {{-- Duration --}}
            <div class="col-md-2">
                <label class="form-label">Duration (Months)</label>
                <input type="number"
                       name="duration_months"
                       class="form-control"
                       value="{{ old('duration_months', $plan->duration_months ?? 0) }}">
            </div>

            {{-- Is Handover --}}
            <div class="col-md-2">
                <label class="form-label">Is Handover?</label>
                <select name="is_handover" class="form-select">
                    <option value="0"
                        {{ old('is_handover', $plan->is_handover ?? 0) == 0 ? 'selected' : '' }}>
                        No
                    </option>
                    <option value="1"
                        {{ old('is_handover', $plan->is_handover ?? 0) == 1 ? 'selected' : '' }}>
                        Yes
                    </option>
                </select>
            </div>

            {{-- Months After Handover --}}
            <div class="col-md-2">
                <label class="form-label">Months After Handover</label>
                <input type="number"
                       name="months_after_handover"
                       class="form-control"
                       value="{{ old('months_after_handover', $plan->months_after_handover ?? '') }}">
            </div>
 
            <div class="col-md-2">
                <label class="form-label">EOI Amount</label>
                <input type="text"
                       name="eoi"
                       class="form-control"
                       value="{{ old('eoi', $plan->eoi ?? '') }}">
            </div>
 
            <div class="col-md-2">
                <label class="form-label">Sort Order</label>
                <input type="number"
                       name="sort_order"
                       class="form-control"
                       value="{{ old('sort_order', $plan->sort_order ?? 0) }}">
            </div>
 
            <div class="col-md-12 text-end mt-3">
                <button class="btn btn-success">
                    {{ isset($plan) ? 'Update Payment Plan' : 'Save Payment Plan' }}
                </button>
                 <a href="{{ route('org.projects.edit', $project->id) }}"
                class="btn btn-link">Cancel</a>
            </div>

        </div>
    </div>
</form>

 <div class="mt-3 card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Payment Plan List</h5>

            <a href="{{ route('org.payment-plan.step.create', encrypt($plan->id)) }}"
            class="btn btn-primary">
                Add Payment Plan Step
            </a>
        </div>

         <table class="table table-striped align-middle" id="PaymentPlanStepTable" style="width:100%;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Percentage</th>
                    <th>Stage Type</th>
                    <th>Start</th>
                    <th>End</th> 
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

      </div>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function () {

     $('#PaymentPlanStepTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('org.payment-plan.step.get') }}",
            type: 'GET',
            data: function (d) {
                d.plan_id = {{ $plan->id }};
            }
        },
        columns: [
            { data: 'id', orderable: false },
            { data: 'name' },
            { data: 'percentage',orderable:false  },
            { data: 'stage_type', },
            { data: 'start', orderable:false },
            { data: 'end', orderable:false },
            { data: 'action', orderable:false },
        ],
        order: [[1, 'asc']],   // name se order
        pageLength: 10
    });

});
</script>
@endsection
