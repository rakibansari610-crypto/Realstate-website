@extends('layouts.admin.master')

@section('main-content') 
<div class="content container-fluid">

  @if($errors->any())
    <div class="alert alert-danger">
      <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="d-flex justify-content-between mb-3">
    <h3>Edit SEO</h3>
    <a href="{{ route('org.seo-meta.index') }}" class="btn btn-link">Back</a>
  </div>

  <form method="POST" action="{{ route('org.seo-meta.update') }}" enctype="multipart/form-data">
    @csrf 
    <input type="hidden" name="id" value="{{ encrypt($seo->id) }}">

    <div class="row g-3">

      {{-- Page Type --}}
      <div class="col-md-4">
        <label>Page Type *</label>
        <select name="page_type" class="form-select" required id="page_type">
          <option value="">Select</option>
          @foreach(['home','buy','rent','project_list','project','developer'] as $type)
            <option value="{{ $type }}" {{ $seo->page_type == $type ? 'selected' : '' }}>
              {{ ucfirst(str_replace('_',' ', $type)) }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Country --}}
      <div class="col-md-4">
        <label>Country</label>
        <select name="country_id" class="form-select">
            <option value="">Select Country</option>
            @foreach($country as $c)
                <option value="{{ $c->id }}" {{ $seo->country_id == $c->id ? 'selected' : '' }}>
                    {{ $c->country_name }}
                </option>
            @endforeach     
        </select>
      </div>

      {{-- Meta --}}
      <div class="col-md-4">
        <label>Meta Title</label>
        <input type="text" name="meta_title" value="{{ $seo->meta_title }}" class="form-control">
      </div>

      <div class="col-md-6">
        <label>Meta Description</label>
        <textarea name="meta_description" class="form-control">{{ $seo->meta_description }}</textarea>
      </div>

      <div class="col-md-6">
        <label>Meta Keywords</label>
        <textarea name="meta_keywords" class="form-control">{{ $seo->meta_keywords }}</textarea>
      </div>

      <hr class="my-4">
      <h5>Open Graph</h5>

      <div class="col-md-12">
        <label>OG Title</label>
        <input type="text" name="og_title" value="{{ $seo->og_title }}" class="form-control">
      </div>

      <div class="col-md-12">
        <label>OG Description</label>
        <textarea name="og_description" class="form-control">{{ $seo->og_description }}</textarea>
      </div>

      <div class="col-md-6">
        <label>OG Image</label>
        <input type="file" name="og_image" class="form-control">
        @if($seo->og_image)
          <img src="{{ asset($seo->og_image) }}" width="100" class="mt-2">
        @endif
      </div>

      <div class="col-md-6">
        <label>OG Image Alt</label>
        <input type="text" name="og_image_alt" value="{{ $seo->og_image_alt }}" class="form-control">
      </div>

      <hr class="my-4">
      <h5>Twitter</h5>

      <div class="col-md-12">
        <label>Twitter Title</label>
        <input type="text" name="twitter_title" value="{{ $seo->twitter_title }}" class="form-control">
      </div>

      <div class="col-md-12">
        <label>Twitter Description</label>
        <textarea name="twitter_description" class="form-control">{{ $seo->twitter_description }}</textarea>
      </div>

      <div class="col-md-6">
        <label>Twitter Image</label>
        <input type="file" name="twitter_image" class="form-control">
        @if($seo->twitter_image)
          <img src="{{ asset($seo->twitter_image) }}" width="100" class="mt-2">
        @endif
      </div>

      <div class="col-md-6">
        <label>Twitter Image Alt</label>
        <input type="text" name="twitter_image_alt" value="{{ $seo->twitter_image_alt }}" class="form-control">
      </div>

      <h5>Schema Markup (JSON-LD)</h5>
      <div>
        <textarea name="schema_json" class="form-control" rows="5">{{ $seo->schema_json }}</textarea>
      </div>

    </div>

    <button class="btn btn-primary mt-3">Update</button>

  </form>
</div>

<script>
document.getElementById('page_type').addEventListener('change', function() {
    let guides = document.querySelectorAll('.projectGuide');

    guides.forEach(function(guide){
        if(document.getElementById('page_type').value === 'project'){
            guide.classList.remove('d-none');
        } else {
            guide.classList.add('d-none');
        }
    });
});
</script>

@endsection