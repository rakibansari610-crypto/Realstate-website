@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="page-title">Units — {{ $project->name }}</h3>
    <a href="{{ route('org.projects.units.create',$project) }}" class="btn btn-primary">Add Unit</a>
  </div>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

  <table class="table table-bordered table-sm align-middle">
    <thead>
      <tr>
        <th>Unit</th>
        <th>Tower</th>
        <th>Floor</th>
        <th>BHK</th>
        <th class="text-end">Area (Carpet/Builtup/Super)</th>
        <th class="text-end">Price</th>
        <th>Status</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($units as $u)
        <tr>
          <td class="fw-semibold">{{ $u->unit_no }}</td>
          <td>{{ $u->tower }}</td>
          <td>{{ $u->floor }}</td>
          <td>{{ $u->bhk }}</td>
          <td class="text-end">
            {{ $u->carpet_area ?? '—' }} / {{ $u->builtup_area ?? '—' }} / {{ $u->super_area ?? '—' }}
          </td>
          <td class="text-end">{{ $u->price ? number_format($u->price,2) : '—' }}</td>
          <td><span class="badge bg-secondary text-capitalize">{{ $u->status }}</span></td>
          <td class="text-end">
            <a href="{{ route('org.projects.units.edit',[$project,$u]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            <form action="{{ route('org.projects.units.destroy',[$project,$u]) }}" method="post" class="d-inline"
                  onsubmit="return confirm('Delete this unit?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="8">No units yet.</td></tr>
      @endforelse
    </tbody>
  </table>

  {{ $units->links() }}

  <div class="mt-3">
    <a href="{{ route('org.projects.edit',$project) }}" class="btn btn-link">Back to Project</a>
    <a href="{{ route('org.projects.materials.index',$project) }}" class="btn btn-outline-info btn-sm">Go to Materials</a>
  </div>
</div>
@endsection
