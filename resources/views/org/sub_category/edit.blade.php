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
    <h3 class="page-title">Add Sub Category </h3>

  </div>

  <form method="post" enctype="multipart/form-data" action="{{route('org.sub_category.update',$sub_category->id)}}" >
    @csrf
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Sub Category Name *</label>
        <input  type="text" name="sub_category_name" class="form-control" value="{{$sub_category->sub_category_name}}" required >
      </div>
      <div class="col-md-3">
        <label class="form-label"> Category Name *</label>
       <select class="form-select" name="category_id" required>
        <option value="">Select Category</option>
        @foreach($category as $ct)
            <option value="{{ $ct->id }}" 
                {{ old('category_id', $sub_category->category_id ?? '') == $ct->id ? 'selected' : '' }}>
                {{ $ct->category_name }}
            </option>
        @endforeach
    </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Icon </label>
        <input type="text" class="form-control"  name="icon" value="{{$sub_category->icon}}">
      </div>

      <div class="col-md-3">
        <label class="form-label">Priority</label>
        <input type="number" class="form-control"  name="priority"  value="{{$sub_category->priority}}">
      </div>
      <div class="col-md-5">
        <label class="form-label">Status </label><br>
        <input type="checkbox" name="active" value="1" {{ ($sub_category->status == 1) ? 'checked' : '' }}>
      </div>
    </div>

    <div class="mt-4">
      <button class="btn btn-primary">Save Changes</button>
      <a href="{{ route('org.category.index') }}" class="btn btn-link">Cancel</a>
    </div>
  </form>
</div>
@endsection
