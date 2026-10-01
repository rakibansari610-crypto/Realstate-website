@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">
  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="page-title">Commissions — {{ $partner->name }}</h3>
    <a href="{{ route('org.partners.index') }}" class="btn btn-link">Back</a>
  </div>

  <div class="card mb-4">
    <div class="card-header">Add / Update Commission</div>
    <div class="card-body">
      <form method="post" action="{{ route('org.partners.commissions.store',$partner) }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Project *</label>
            <select name="project_id" class="form-select" required>
              <option value="">-- Select --</option>
              @foreach($projects as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Type *</label>
            <select name="type" class="form-select" required>
              <option value="percent">% Percent</option>
              <option value="flat">Flat</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Value *</label>
            <input type="number" step="0.01" name="value" class="form-control" required>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="active" value="1" id="cActive" checked>
              <label class="form-check-label" for="cActive">Active</label>
            </div>
          </div>

          <div class="col-md-3">
            <label class="form-label">Effective From</label>
            <input type="date" name="effective_from" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Effective To</label>
            <input type="date" name="effective_to" class="form-control">
          </div>
        </div>
        <div class="mt-3"><button class="btn btn-primary">Save</button></div>
      </form>
    </div>
  </div>

  <table class="table table-striped">
    <thead><tr><th>Project</th><th>Type</th><th>Value</th><th>Effective</th><th>Active</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
      @forelse($commissions as $c)
        <tr>
          <td>{{ $c->project?->name }}</td>
          <td class="text-capitalize">{{ $c->type }}</td>
          <td>{{ $c->value }}</td>
          <td class="small">
            {{ $c->effective_from ? $c->effective_from->format('d M Y') : '—' }} —
            {{ $c->effective_to ? $c->effective_to->format('d M Y') : '—' }}
          </td>
          <td>{!! $c->active ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
          <td class="text-end">
            <form action="{{ route('org.partners.commissions.destroy',[$partner,$c]) }}" method="post" onsubmit="return confirm('Delete this commission?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6">No commissions yet.</td></tr>
      @endforelse
    </tbody>
  </table>

  {{ $commissions->links() }}
</div>
@endsection
