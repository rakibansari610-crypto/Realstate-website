@extends('layouts.admin.master')

@section('main-content')
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Projects</h4>
                <div class="page-title-right">
                     @if(!auth()->user()->hasAnyRole(['channel partner']))
                    <a href="{{ route('org.projects.create') }}" class="btn btn-primary">Add Project</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
      <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped align-middle" style="width: 100%" id="project-table">
                    <thead>
                      <tr>
                        <th>Sn</th>
                        <th>Project</th>
                        <th>Developer</th>
                        <th>Price</th>
                        <th>Area / Location</th>
                        @hasanyrole('org admin|admin')
                        <th>Created By</th>
                        @endrole
                        <th>Status</th>
                        <th>Cover</th>
                        <th class="text-end">Actions</th>
                      </tr>
                    </thead>
                    {{-- <tbody>
                      @forelse($projects as $p)
                        <tr>
                          <td>
                            <div class="fw-semibold">{{ $p->name }}</div>
                            <div class="text-muted small">{{ $p->city }} {{ $p->location ? '• '.$p->location : '' }}</div>
                          </td>
                          <td>{{ $p->developer?->name }}</td>
                          <td> {{ rtrim(rtrim($p->min_price, '0'), '.') }} - {{ rtrim(rtrim($p->max_price, '0'), '.') }}</td>
                          <td>{{ $p->location}} - {{$p->city}}</td>
                          @hasanyrole('org admin|admin')
                        <td>{{ $p->creator->name ?? '—' }}</td>
                        @endrole
                          <td><span class="badge bg-secondary text-capitalize">{{ str_replace('_',' ',$p->status) }}</span></td>
                          <td>
                            @if($p->cover_image_path)
                              <img src="{{ Storage::disk('public')->url($p->cover_image_path) }}" style="height:40px;border-radius:4px;">
                            @endif
                          </td>
                          <td class="text-end">
                            @if(!auth()->user()->hasAnyRole(['channel partner']))
                            <a href="{{ route('org.projects.edit',$p) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            @endif
                            <a href="{{ route('org.projects.view',$p) }}" class="btn btn-sm btn-outline-success">View</a>
                            @if(!auth()->user()->hasAnyRole(['channel partner']))
                            <a href="{{ route('org.projects.units.index',$p) }}" class="btn btn-sm btn-outline-primary">Unit</a>
                            @endif
                            <a href="{{ route('org.projects.materials.index',$p) }}" class="btn btn-sm btn-outline-info">Materials</a>
                            @if(!auth()->user()->hasAnyRole(['data entry', 'channel partner']))
                            <form action="{{ route('org.projects.destroy',$p) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this project?')">
                              @csrf @method('DELETE')
                              <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                            @endif
                          </td>
                        </tr>
                      @empty
                        <tr><td colspan="5">No projects found.</td></tr>
                      @endforelse
                    </tbody> --}}
                  </table> 
                </div>
            </div>
        </div>
      </div>
    </div>
@endsection
@section('scripts')
<script>
$(document).ready(function() {
    $('#project-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('org.projects.get') }}", 
        columns: [
            { data: 0, name: 'id' },               
            { data: 1, name: 'name' },             
            { data: 2, name: 'developer_id' },     
            { data: 3, name: 'min_price', orderable: false, searchable: false }, 
            { data: 4, name: 'location' },    
            @hasanyrole('org admin|admin')
            { data: 5, name: 'creator_id' },      
            @endrole
            { data: 6, name: 'status', orderable: false,  },
            { data: 7, name: 'cover', orderable: false, searchable: false },   
            { data: 8, name: 'actions', orderable: false, searchable: false } 
        ],
        order: [[0, 'desc']]
    });
});
</script>

@endsection
