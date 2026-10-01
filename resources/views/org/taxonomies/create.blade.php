@extends('layouts.admin.master')

@section('main-content')
<h3>Create {{ ucfirst($module) }} Category</h3>

<form method="POST" action="{{ route('taxonomies.store', $module) }}">
    @csrf
    <div class="row">
        <div class="col-md-3 mb-3">
            <label for="parent_id">Parent Category (Optional)</label>
            <select id="parent_id" name="parent_id" class="form-select">
                <option value="">-- None --</option>
                @foreach($parenttaxonomies as $parent)
                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                    {{ $parent->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="name">Name*</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>







        <div class="col-md-3 mb-3">
            <label for="priority">Priority</label>
            <input type="number" id="priority" name="priority" min="0" class="form-control" value="{{ old('priority', 0) }}">
        </div>

        <div class="col-md-3 mb-3">
            <label for="status">Status*</label>
            <select id="status_id" name="status" class="form-select" required>
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>



        <input type="hidden" name="module" value="{{ $module }}">
        <div class="col-md-12 mb-3">
            <button type="submit" class="btn btn-secondary">Create</button>
            <a href="{{ route('taxonomies.index', $module) }}" class="btn btn-warning">Cancel</a>
        </div>
    </div>
</form>
@if ($errors->has('error'))
    <div class="alert alert-danger">
        {{ $errors->first('error') }}
    </div>
@endif
@endsection
