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

<h3 class="page-title mb-3">Edit Project Media</h3>

    <form method="POST" action="{{ route('org.project-media.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="project_id" value="{{ $project->id }}">
    <input type="hidden" name="id" value="{{ $media->id }}">

        <div class="row g-3">

            {{-- Collection --}}
            <div class="col-md-3">
                <label class="form-label">Collection</label>
                <select name="collection" class="form-select" required>
                    <option value="">Select</option>

                    <option value="interior"
                        {{ old('collection', $media->collection) == 'interior' ? 'selected' : '' }}>
                        Interior
                    </option>

                    <option value="architecture"
                        {{ old('collection', $media->collection) == 'architecture' ? 'selected' : '' }}>
                        Architecture
                    </option>

                    <option value="lobby"
                        {{ old('collection', $media->collection) == 'lobby' ? 'selected' : '' }}>
                        Lobby
                    </option>

                    <option value="general_plan"
                        {{ old('collection', $media->collection) == 'general_plan' ? 'selected' : '' }}>
                        General Plan
                    </option>

                    <option value="brochure"
                        {{ old('collection', $media->collection) == 'brochure' ? 'selected' : '' }}>
                        Brochure
                    </option>
                    <option value="videos"
                        {{ old('collection', $media->collection) == 'videos' ? 'selected' : '' }}>
                        Videos
                    </option>

                </select>
            </div>


            {{-- Upload File --}}
            {{-- <div class="col-md-3">
                <label class="form-label">Upload File</label>
                <input type="file" name="file" class="form-control" >
                <img src="{{$media->url}}" height="100" width="100" class="mt-4">
            </div> --}}
            <div class="col-md-3">

                @if($media->collection == 'videos')

                    <label class="form-label">Video Link</label>

                    <input type="text"
                        name="video_link"
                        value="{{ $media->url }}"
                        class="form-control"
                        placeholder="Enter YouTube / Video Link">

                    <div class="mt-3">
                        <a href="{{ $media->url }}" target="_blank">
                            {{ $media->url }}
                        </a>
                    </div>

                @else

                    <label class="form-label">Upload File</label>

                    <input type="file" name="file" class="form-control">

                    <div class="mt-3">
                        <img src="{{ $media->url }}" height="100" width="100">
                    </div>

                @endif

                </div>

            {{-- Sort Order --}}
            <div class="col-md-2">
                <label class="form-label">Sort Order</label>
                <input type="number"
                      name="sort_order"
                      value="{{$media->sort_order}}"
                      class="form-control">
            </div>

            

            {{-- Submit --}}
            <div class="col-md-4">
                <div class="mt-4 ">
                    <button class="btn btn-primary">
                        Update Media
                    </button>
                    <a href="{{ route('org.projects.edit', $project->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection 
