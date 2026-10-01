@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">
  <h3 class="page-title">Assign Lead: {{ $lead->name }}</h3>

  <form method="post" action="{{ route('org.leads.assign.update',$lead) }}" class="mt-3">
    @csrf @method('PUT')

    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Assign to (Channel Partner / Broker / Agent)</label>
        <select name="assigned_to" class="form-select">
          <option value="">-- Unassigned --</option>
          @foreach($assignables as $u)
            <option value="{{ $u->id }}" @selected($lead->assigned_to==$u->id)>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="mt-3">
      <button class="btn btn-primary">Save Assignment</button>
      <a href="{{ route('org.leads.edit',$lead) }}" class="btn btn-link">Cancel</a>
    </div>
  </form>
</div>
@endsection
