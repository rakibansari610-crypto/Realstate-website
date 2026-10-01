@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">

    @php
        $isEdit = isset($testimonial) && isset($testimonial->id);
    @endphp

    {{-- Success --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="page-title">
            {{ $isEdit ? 'Edit Testimonial' : 'Add Testimonial' }}
        </h3>
        <a href="{{ route('org.testimonial-index') }}" class="btn btn-secondary">Back</a>
    </div>

    <form method="POST"
          enctype="multipart/form-data"
          action="{{ route('org.testimonial-store') }}">
        @csrf
            @if($isEdit)
            <input type="hidden" name="id" value="{{ $testimonial->id }}">
        @endif
        <div class="row g-3">

            {{-- Name --}}
            <div class="col-md-4">
                <label class="form-label">Name</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name', $isEdit ? $testimonial->name : '') }}">
            </div>

            {{-- Designation --}}
            <div class="col-md-4">
                <label class="form-label">Designation</label>
                <input type="text"
                       name="designation"
                       class="form-control"
                       value="{{ old('designation', $isEdit ? $testimonial->designation : '') }}">
            </div>

            {{-- Rating --}}
            <div class="col-md-4">
                <label class="form-label">Rating (1-5)</label>
                <input type="number"
                       name="rating_number"
                       min="1"
                       max="5"
                       class="form-control"
                       value="{{ old('rating_number', $isEdit ? $testimonial->rating_number : '') }}">
            </div>

            {{-- Profile Image --}}
            <div class="col-md-6">
                <label class="form-label">Profile Image</label>
                <input type="file" name="profile" class="form-control" accept="image/jpeg,image/png,image/webp">

                @if($isEdit && !empty($testimonial->profile))
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$testimonial->profile) }}" width="120" class="rounded shadow d-block">
                        <div style="margin-left:50px;">
                            <a href="{{route('org.testimonial-profile-destroy', encrypt($testimonial->id))}}"
                            class="text-danger d-inline-block mt-2 text-center"
                            onclick="return confirm('Are you sure you want to delete this image?')">
                                <i class="las la-trash"></i> 
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sort Order --}}
            <div class="col-md-6">
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $isEdit ? $testimonial->sort_order : '') }}">
            </div>

            {{-- Description --}}
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description"
                          class="form-control"
                          rows="4">{{ old('description', $isEdit ? $testimonial->description : '') }}</textarea>
            </div>

        </div>

        {{-- Active --}}
        <div class="mt-4">
            <label class="form-label">Active</label><br>
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   {{ old('is_active', $isEdit ? $testimonial->is_active : true) ? 'checked' : '' }}>
        </div>

        {{-- Submit --}}
        <div class="mt-4">
            <button class="btn btn-primary px-4">
                {{ $isEdit ? 'Update Testimonial' : 'Create Testimonial' }}
            </button>
        </div>

    </form>

</div>
@endsection
