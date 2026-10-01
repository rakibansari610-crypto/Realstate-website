@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="page-title">Marketing Materials — {{ $project->name }}</h3>
    <a href="{{ route('org.projects.edit',$project) }}" class="btn btn-link">Back to Project</a>
  </div>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif



   @if(!auth()->user()->hasAnyRole(['channel partner']))
  <div class="card mb-4">
    <div class="card-header">Add Material</div>
    <div class="card-body">
      <form method="post" enctype="multipart/form-data" action="{{ route('org.projects.materials.store',$project) }}" id="materialForm">
        @csrf
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Title *</label>
            <input name="title" class="form-control" required value="{{ old('title') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Type *</label>
            <select name="type" id="matType" class="form-select" required>
              @foreach(['image','pdf','doc','video','link'] as $t)
                <option value="{{ $t }}" @selected(old('type')==$t)>{{ ucfirst($t) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-5">
            <label class="form-label">File / URL *</label>
            <input type="file" name="file" id="fileInput" class="form-control mb-2" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx">
            <input type="url" name="url" id="urlInput" class="form-control" placeholder="https://..." style="display:none;">
            <div class="form-text" id="fileHelp">Upload image/pdf/doc OR switch to URL for video/link.</div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Tags (comma-separated)</label>
            <input type="text" id="tagsInput" class="form-control" placeholder="e.g. brochure, price, 2bhk">
            <input type="hidden" name="tags[]" id="tagsHidden"> {{-- will be split by JS --}}
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_public" value="1" id="isPublic" checked>
              <label class="form-check-label" for="isPublic">Public</label>
            </div>
          </div>
        </div>

        <div class="mt-3">
          <button class="btn btn-primary">Add</button>
        </div>
      </form>
    </div>
  </div>
@endif
  <div class="row g-3">
    @forelse($materials as $m)
      <div class="col-md-3">
        <div class="card h-100">
          @if($m->type === 'image' && $m->file_path)
            <img src="{{ $m->file_url }}" class="card-img-top" alt="">
          @endif
          <div class="card-body">
            <div class="fw-semibold">{{ $m->title }}</div>
            <div class="text-muted small text-uppercase">{{ $m->type }}</div>

            @if(in_array($m->type,['pdf','doc']) && $m->file_path)
              <a class="d-inline-block mt-2" href="{{ $m->file_url }}" target="_blank">Download file</a>
            @endif
            @if(in_array($m->type,['video','link']) && $m->url)
              <a class="d-inline-block mt-2" href="{{ $m->url }}" target="_blank">Open link</a>
            @endif

            @if($m->tags)
              <div class="mt-2">
                @foreach($m->tags as $tag)
                  <span class="badge bg-light text-dark border me-1">{{ $tag }}</span>
                @endforeach
              </div>
            @endif
          </div>
           @if(!auth()->user()->hasAnyRole(['channel partner']))
          <div class="card-footer text-end">
            <form action="{{ route('org.projects.materials.destroy',[$project,$m]) }}" method="post"
                  onsubmit="return confirm('Delete this material?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </div>
          @endif
        </div>
      </div>
    @empty
      <div class="col-12"><em>No materials yet.</em></div>
    @endforelse
  </div>

  <div class="mt-3">{{ $materials->links() }}</div>
</div>

{{-- Simple JS to toggle file/url fields and split tags into array --}}
<script>
  const matType   = document.getElementById('matType');
  const fileInput = document.getElementById('fileInput');
  const urlInput  = document.getElementById('urlInput');
  const fileHelp  = document.getElementById('fileHelp');
  const form      = document.getElementById('materialForm');
  const tagsIn    = document.getElementById('tagsInput');
  const tagsHidden= document.getElementById('tagsHidden');

  function toggleIO(){
    const t = matType.value;
    const useUrl = (t === 'video' || t === 'link');
    fileInput.style.display = useUrl ? 'none' : 'block';
    urlInput.style.display  = useUrl ? 'block' : 'none';
    fileInput.required = !useUrl;
    urlInput.required  = useUrl;
    fileHelp.textContent = useUrl ? 'Provide a valid URL (YouTube, drive link, etc.)' : 'Upload image/pdf/doc';
  }
  matType.addEventListener('change', toggleIO);
  toggleIO();

  form.addEventListener('submit', function(){
    const raw = (tagsIn.value || '').split(',').map(s => s.trim()).filter(Boolean);
    // Send as JSON-y array: set multiple hidden inputs
    const wrap = document.createElement('div');
    raw.forEach(v => {
      const h = document.createElement('input');
      h.type = 'hidden';
      h.name = 'tags[]';
      h.value = v;
      wrap.appendChild(h);
    });
    // Remove original tagsHidden so we don't send empty value
    tagsHidden.remove();
    form.appendChild(wrap);
  });
</script>
@endsection
