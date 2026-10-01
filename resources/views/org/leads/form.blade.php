@extends('layouts.admin.master')

@section('main-content')
@php $disabled = isset($isView) && $isView ? 'disabled' : ''; @endphp
<div class="content container-fluid">

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Please fix the following:</div>
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="page-title">{{ $lead->exists ? 'Edit Lead' : 'Add Lead' }}</h3>
    <div>
      <a href="{{ route('org.leads.index') }}" class="btn btn-link">Back to Leads</a>
      @if($lead->exists)
        @role('org admin')
          <a href="{{ route('org.leads.assign',$lead) }}" class="btn btn-outline-primary btn-sm">Assign/Reassign</a>
          <a href="{{ route('org.leads.materialise',$lead) }}" class="btn btn-outline-success btn-sm">Materialise</a>
        @endrole
      @endif
    </div>
  </div>

  <form method="post" action="{{ $lead->exists ? route('org.leads.update',$lead) : route('org.leads.store') }}">
    @csrf
    @if($lead->exists) @method('PUT') @endif

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Name *</label>
        <input name="name"  type="text" class="form-control" required value="{{ old('name',$lead->name) }}" {{ $disabled }}>
      </div>
      @if(!$lead->exists || !auth()->user()->hasRole('channel partner'))
      <div class="col-md-4">
        <label class="form-label">Phone</label>
        <input name="phone" type="number" class="form-control" value="{{ old('phone',$lead->phone) }}" {{ $disabled }}>
      </div>
      @endif
      <div class="col-md-4">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" value="{{ old('email',$lead->email) }}" {{ $disabled }}>
      </div>

      <div class="col-md-3">
        <label class="form-label">Alt Phone</label>
        <input name="alt_phone"  type="number" class="form-control" value="{{ old('alt_phone',$lead->alt_phone) }}" {{ $disabled }}>
      </div>
      <div class="col-md-3">
        <label class="form-label">Source</label>
        <input name="source"   type="text"class="form-control" value="{{ old('source',$lead->source) }}" placeholder="Portal / Referral / Walk-in" {{ $disabled }}>
      </div>
      <div class="col-md-3">
        <label class="form-label">Status *</label>
        <select name="status" class="form-select" required {{ $disabled }}>
          @foreach(['new','contacted','site_visit','booked','lost'] as $s)
            <option value="{{ $s }}" @selected(old('status',$lead->status ?? 'new')==$s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Follow-up At</label>
        <input type="datetime-local" name="follow_up_at" class="form-control"
               value="{{ old('follow_up_at', $lead->follow_up_at ? $lead->follow_up_at->format('Y-m-d\TH:i') : '') }}" {{ $disabled }}>
      </div>

      <div class="col-md-3">
        <label class="form-label">Preferred BHK</label>
        <input name="preferred_bhk"  type="text" class="form-control" value="{{ old('preferred_bhk',$lead->preferred_bhk) }}" {{ $disabled }}>
      </div>
      <div class="col-md-3">
        <label class="form-label">Min Budget</label>
        <input type="number" step="0.01" name="budget_min" class="form-control" value="{{ old('budget_min',$lead->budget_min) }}" {{ $disabled }}>
      </div>
      <div class="col-md-3">
        <label class="form-label">Max Budget</label>
        <input type="number" step="0.01" name="budget_max" class="form-control" value="{{ old('budget_max',$lead->budget_max) }}" {{ $disabled }}>
      </div>

      <div class="col-md-3">
        <label class="form-label">Property Type</label>
        <input type="text" name="property_type" class="form-control" value="{{ old('property_type',$lead->property_type) }}" {{ $disabled }}>
      </div>

      <div class="col-md-3">
        <label class="form-label">Buildup Status</label>
        <input type="text" name="property_build" class="form-control" value="{{ old('property_build',$lead->property_build) }}">
      </div>

      <div class="col-md-3">
        <label class="form-label">Timeframe</label>
        <input type="text" name="timeframe" class="form-control" value="{{ old('timeframe',$lead->timeframe) }}" {{ $disabled }}>
      </div>

      <div class="col-md-3">
        <label class="form-label">Preferred Langugae</label>
        <input type="text" name="pref_langugae" class="form-control" value="{{ old('pref_langugae',$lead->pref_langugae) }}" {{ $disabled }}>
      </div>

      <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="3" {{ $disabled }}>{{ old('notes',$lead->notes) }}</textarea>
      </div>
    </div>
    @if(!isset($isView) || !$isView)
    <div class="mt-4">
      <button class="btn btn-primary">{{ $lead->exists ? 'Save Changes' : 'Create Lead' }}</button>
    </div>
    @endif
  </form>
</div>
@endsection
