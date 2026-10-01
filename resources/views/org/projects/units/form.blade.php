@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">
  <h3 class="page-title">{{ $unit->exists ? 'Edit Unit' : 'Add Unit' }} — {{ $project->name }}</h3>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

   @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

   

  <form method="post"
        action="{{ $unit->exists
            ? route('org.projects.units.update',[$project,$unit])
            : route('org.projects.units.store',$project) }}">
    @csrf
    @if($unit->exists) @method('PUT') @endif

    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Unit No *</label>
        <input name="unit_no" class="form-control" required value="{{ old('unit_no',$unit->unit_no) }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tower</label>
        <input name="tower" class="form-control" value="{{ old('tower',$unit->tower) }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Floor</label>
        <input name="floor" class="form-control" value="{{ old('floor',$unit->floor) }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">BHK</label>
        <input name="bhk" class="form-control" value="{{ old('bhk',$unit->bhk) }}">
      </div>

      <div class="col-md-4">
        <label class="form-label">Carpet Area</label>
        <input type="number" step="0.01" name="carpet_area" class="form-control" value="{{ old('carpet_area',$unit->carpet_area) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Built-up Area</label>
        <input type="number" step="0.01" name="builtup_area" class="form-control" value="{{ old('builtup_area',$unit->builtup_area) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Super Area</label>
        <input type="number" step="0.01" name="super_area" class="form-control" value="{{ old('super_area',$unit->super_area) }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Starting Price</label>
        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price',$unit->price) }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Status *</label>
        <select name="status" class="form-select" required>
          @foreach(['available','blocked','booked','sold'] as $s)
            <option value="{{ $s }}" @selected(old('status',$unit->status ?? 'available')==$s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Facing</label>
        <input name="facing" class="form-control" value="{{ old('facing',$unit->facing) }}">
      </div>
      <div class="col-12">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" rows="3" class="form-control">{{ old('remarks',$unit->remarks) }}</textarea>
      </div>
    </div>

    <div class="mt-4">
      <button class="btn btn-primary">{{ $unit->exists ? 'Save Changes' : 'Add Unit' }}</button>
      <a href="{{ route('org.projects.units.index',$project) }}" class="btn btn-link">Cancel</a>
    </div>
  </form>
</div>
@endsection
