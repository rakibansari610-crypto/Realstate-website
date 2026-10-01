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

<h3 class="page-title mb-3">Edit Project Map Plan</h3>
    <form method="POST" action="{{ route('org.near-by-plan.update', encrypt($map->id)) }}">
        @csrf
        <input type="hidden" name="project_id" value="{{ $project->id }}">
        <div class="card mb-4">
            <div class="card-header">
                <strong>Project Map Plan</strong>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-3">
                    <label class="form-label"> Name</label>
                    <input type="text" name="name" value="{{ old('name', $map->name ?? '') }}" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Distance</label>
                    <input type="number" name="distance" min="0" step="0.01" class="form-control" value="{{ old('distance', $map->distance ?? '') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Time</label>
                    <input type="number" name="time" min="0" value="{{ old('time', $map->time ?? '') }}" class="form-control">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" min="0" {{ old('sort_order', $map->sort_order ?? '') }} class="form-control" value="0">
                </div>
               <div class="col-md-3" style="margin-top: 40px"> 
                        <button class="btn btn-success">Update  </button>
                        <a href="{{ route('org.projects.edit', $project->id ?? $typical->project_id) }}"
                        class="btn btn-link">Cancel</a> 
            </div>
            </div>
            
        </div>
    </form>

 
@endsection
@section('scripts')
<script>
 
</script>
@endsection
