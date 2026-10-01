@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">

    @php
        $isEdit = isset($blog) && isset($blog->id);
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
            {{ $isEdit ? 'Edit Blog' : 'Add Blog' }}
        </h3>
        <a href="{{ route('org.blogs-index') }}" class="btn btn-secondary">Back</a>
    </div>

    <form method="POST"
          action="{{ $isEdit ? route('org.blogs-update', encrypt($blog->id)) : route('org.blogs-store') }}"
          enctype="multipart/form-data">

        @csrf 

        <div class="row">
           
            {{-- Blog Title --}}
            <div class="col-md-3 mb-3 mt-3">
                            <label class="form-label">Country *</label>
                            <select name="country_id" id="country_id" class="form-select" required>
                                <option value="">-- Select Country --</option>
                              @foreach ($countries as $country)
    <option value="{{ $country->id }}"
        @selected(old('country_id', $blog->country_id ?? null) == $country->id)>
        {{ $country->country_name }}
    </option>
@endforeach
                            </select>
                        </div>
            <div class="col-md-9 mb-3 mt-3">
                <label class="form-label">Blog Title *</label>
                <input type="text"
                       name="title"
                       class="form-control"
                       value="{{ old('title', $blog->title ?? '') }}"
                       required>
            </div>
            @if ($blog->exists)
                <div class="mb-2">
                    <label class="form-label">Slug *</label><br>
                    <input type="checkbox" name="autoSlug" id="autoSlug" class="form-check-input"
                        {{ $blog->is_auto_slug ? 'checked' : '' }}>
                    <label for="autoSlug">Auto Generate</label>

                    <input type="text" id="slugInput" name="blog_slug" class="form-control mt-2"
                        value="{{ old('slug', $blog->slug) }}">
                </div>
            @endif

             <div class="col-md-12 mb-3">
                <label class="form-label">Meta Title </label>
                <input name="meta_title" class="form-control"
                    value="{{ old('meta_title', $blog->meta_title ?? '') }}" >
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Meta Description </label>
                <input name="meta_description" class="form-control"
                    value="{{ old('meta_description', $blog->meta_description ?? '') }}" >
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Meta Keywords </label>
                <input name="meta_keywords" class="form-control"
                    value="{{ old('meta_keywords', $blog->meta_keywords ?? '') }}" >
            </div>
            {{-- Category --}}
            {{-- <div class="col-md-6 mb-3">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">Select Category</option>
                    @foreach(['cover','interior','architecture','lobby','general_plan','brochure'] as $cat)
                        <option value="{{ $cat }}"
                            {{ old('category', $blog->category ?? '') == $cat ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_',' ', $cat)) }}
                        </option>
                    @endforeach
                </select>
            </div> --}}

            {{-- Featured Image --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Featured Image</label>
                <input type="file"
                       name="featured_image"
                       class="form-control">

                @if($isEdit && $blog->featured_image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$blog->featured_image) }}" width="100">
                    </div>
                @endif
            </div>

            {{-- Detailed Image --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Banner</label>
                <input type="file"
                       name="detailed_image"
                       class="form-control">

                @if($isEdit && $blog->detailed_image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$blog->detailed_image) }}" width="100">
                    </div>
                @endif
            </div>

            {{-- Short Description --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Short Description</label>
                <textarea name="short_description"
                          class="form-control summernote"
                          rows="3">{{ old('short_description', $blog->short_description ?? '') }}</textarea>
            </div>

            {{-- Content --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Description</label>
                <textarea name="content" id="summernote"
                          class="form-control summernote"
                          rows="5">{{ old('content', $blog->content ?? '') }}</textarea>
            </div>

            {{-- Priority --}}
            <div class="col-md-3 mb-3">
                <label class="form-label">Priority</label>
                <input type="number"
                       name="sort_order"
                       class="form-control"
                       value="{{ old('sort_order', $blog->sort_order ?? 0) }}">
            </div>

            {{-- Status --}}
            <div class="col-md-3 mb-3">
                <label class="form-label">Status *</label>
                <select name="status" class="form-select" required>
                    <option value="">Select Status</option>
                    <option value="draft"
                        {{ old('status', $blog->status ?? '') == 'draft' ? 'selected' : '' }}>
                        Draft
                    </option>
                    <option value="published"
                        {{ old('status', $blog->status ?? '') == 'published' ? 'selected' : '' }}>
                        Published
                    </option>
                </select>
            </div>

            {{-- Active --}}
            <div class="col-md-3 d-flex align-items-end mb-3">
                <div class="form-check">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           class="form-check-input"
                           id="blog_status"
                           {{ old('is_active', $blog->is_active ?? 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="blog_status">
                        Active
                    </label>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="col-md-12 d-flex align-items-end justify-content-end mb-3">
                <button class="btn btn-primary me-2">
                    {{ $isEdit ? 'Update' : 'Save' }}
                </button>
                <a href="{{ route('org.blogs-index') }}"
                   class="btn btn-light">
                   Cancel
                </a>
            </div>

        </div>
    </form>

</div>
@endsection
@section('scripts')
    <script>
        //slug work

        function toggleSlug() {
            if ($('#autoSlug').is(':checked')) {
                $('#slugInput').val("{{ $blog->slug }}"); // real slug show
                $('#slugInput').prop('disabled', true);
            } else {
                $('#slugInput').prop('disabled', false);
            }
        }

        // page load par check
        toggleSlug();

        // checkbox change par
        $('#autoSlug').change(function() {
            toggleSlug();
        });
    </script>
@endsection
