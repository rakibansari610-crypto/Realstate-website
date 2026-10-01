@extends('layouts.admin.master')

@section('main-content') 
<div class="content container-fluid">

  {{-- Errors --}}
  @if($errors->any())
    <div class="alert alert-danger">
      <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  {{-- Header --}}
  <div class="d-flex justify-content-between mb-3">
    <h3>Add SEO</h3>
    <a href="{{ route('org.seo-meta.index') }}" class="btn btn-link">Back</a>
  </div>

  {{-- Form --}}
  <form method="POST" action="{{ route('org.seo-meta.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-3">

      {{-- Page Type --}}
      <div class="col-md-4">
        <label>Page Type *</label>
        <select name="page_type" class="form-select" required id="page_type">
          <option value="">Select</option>
          <option value="home">Home</option>
          <option value="buy">Buy</option>
          <option value="rent">Rent</option>
          <option value="project_list">Project List</option>
          <option value="project">Project</option>
          <option value="developer">Developer</option>
        </select>
      </div>

      {{-- Country --}}
      <div class="col-md-4">
        <label>Country</label>
        <select name="country_id" id="country_id" class="form-select">
            <option value="">Select Country</option>
            @foreach($country as $c)
                <option value="{{ $c->id }}">{{ $c->country_name }}</option>
            @endforeach     
        </select>
      </div>

      
      {{-- Meta Title --}}
      <div class="col-md-4">
        <label>Meta Title</label>
        <input type="text" name="meta_title" class="form-control">
        <small class="text-muted d-none projectGuide">
          👉 Use dynamic tags: <b>{project_name}</b>, <b>{city}</b>, <b>{developer}</b>
        </small>
      </div>

      {{-- Meta Description --}}
      <div class="col-md-6">
        <label>Meta Description</label>
        <textarea name="meta_description" class="form-control"></textarea>
         <small class="text-muted d-none projectGuide">
          👉 Use dynamic tags: <b>{project_name}</b>, <b>{city}</b>, <b>{developer}</b>
        </small>
      </div>

      {{-- Meta Keywords --}}
      <div class="col-md-6">
        <label>Meta Keywords</label>
        <textarea name="meta_keywords" class="form-control"></textarea>
         <small class="text-muted d-none projectGuide">
          👉 Use dynamic tags: <b>{project_name}</b>, <b>{city}</b>, <b>{developer}</b>
        </small>
      </div>

      <hr class="my-4">
      <h5>Open Graph</h5>
      <div class="col-md-12">
        <label>OG Title</label>
        <input type="text" name="og_title" class="form-control">
      </div>

      <div class="col-md-12">
        <label>OG Description</label>
        <textarea name="og_description" class="form-control"></textarea>
      </div>

      <div class="col-md-6">
        <label>OG Image</label>
        <input type="file" name="og_image" class="form-control">
      </div>

      <div class="col-md-6">
        <label>OG Image Alt</label>
        <input type="text" name="og_image_alt" class="form-control">
      </div>

      <hr class="my-4">
      <h5>Twitter</h5>

       <div class="col-md-12">
        <label>Twitter Title</label>
        <input type="text" name="twitter_title" class="form-control">
      </div>

      <div class="col-md-12">
        <label>Twitter Description</label>
        <textarea name="twitter_description" class="form-control"></textarea>
      </div>

      <div class="col-md-6">
        <label>Twitter Image</label>
        <input type="file" name="twitter_image" class="form-control">
      </div>

      <div class="col-md-6">
        <label>Twitter Image Alt</label>
        <input type="text" name="twitter_image_alt" class="form-control">
      </div>
      <h5>Schema Markup (JSON-LD)</h5>
      <div>
        <label>Schema Markup (JSON-LD)</label>
        <textarea name="schema_markup" class="form-control" rows="5"></textarea>
      </div>
    </div>
    <button class="btn btn-primary mt-3">Save</button>

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