@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Please fix the following:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="page-title">Add Country</h3>
  </div>

  <form method="post" action="{{ route('org.country.store') }}">
    @csrf
    <div class="row g-3">

      <div class="col-md-3">
        <label class="form-label">Country Name *</label>
        <input type="text" name="country_name" class="form-control" required>
      </div>

      <div class="col-md-3">
        <label class="form-label">Currency</label>
        <input type="text" name="country_currency" class="form-control">
      </div>

      <div class="col-md-3">
        <label class="form-label">Language</label>
        <input type="text" name="country_lang" class="form-control">
      </div>

      <div class="col-md-3">
        <label class="form-label">Country Code</label>
        <input type="text" name="country_code" class="form-control">
      </div>

      <div class="col-md-3">
        <label class="form-label">ISO Code</label>
        <input type="text" name="country_iso" class="form-control">
      </div>

      <div class="col-md-3">
        <label class="form-label">ISD Code</label>
        <input type="text" name="country_isd" class="form-control">
      </div>
<div class="col-md-3">
    <label class="form-label">Default Country</label><br>
    <input type="checkbox" name="country_default" value="1">
</div>

      <div class="col-md-3">
        <label class="form-label">Priority</label>
        <input type="number" name="country_priority" class="form-control">
      </div>

      <div class="col-md-3">
        <label class="form-label">Status</label><br>
        <input type="checkbox" name="active" value="1">
      </div>

    </div>

    <div class="mt-4">
      <button class="btn btn-primary">Save Changes</button>
      <a href="{{ route('org.country.index') }}" class="btn btn-link">Cancel</a>
    </div>
  </form>
</div>
@endsection
