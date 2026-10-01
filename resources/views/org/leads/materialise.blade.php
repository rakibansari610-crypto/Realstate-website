@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">
  <h3 class="page-title">Materialise Lead: {{ $lead->name }}</h3>

  <form method="post" action="{{ route('org.leads.materialise.store',$lead) }}" class="mt-3" id="matForm">
    @csrf

    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Project *</label>
        <select name="project_id" id="projectSelect" class="form-select" required>
          <option value="">-- Select Project --</option>
          @foreach($projects as $p)
            <option value="{{ $p->id }}" @selected(old('project_id',$lead->project_id)==$p->id)>{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Unit (optional)</label>
        <select name="project_unit_id" id="unitSelect" class="form-select">
          <option value="">-- None --</option>
          @foreach($units as $u)
            <option value="{{ $u->id }}" @selected(old('project_unit_id',$lead->project_unit_id)==$u->id)>{{ $u->unit_no }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="mt-3">
      <button class="btn btn-primary">Materialise</button>
      <a href="{{ route('org.leads.edit',$lead) }}" class="btn btn-link">Cancel</a>
    </div>
  </form>
</div>

<script>
  const projectSelect = document.getElementById('projectSelect');
  const unitSelect    = document.getElementById('unitSelect');

  async function refreshUnits(projectId) {
    unitSelect.innerHTML = '<option value="">-- None --</option>';
    if (!projectId) return;
    try {
      const res = await fetch(`{{ url('org-admin/projects') }}/${projectId}/units/options`);
      const data = await res.json();
      data.forEach(u => {
        const opt = document.createElement('option');
        opt.value = u.id; opt.textContent = u.unit_no;
        unitSelect.appendChild(opt);
      });
    } catch (e) { console.error(e); }
  }

  projectSelect.addEventListener('change', (e) => {
    unitSelect.value = '';
    refreshUnits(e.target.value);
  });
</script>
@endsection
