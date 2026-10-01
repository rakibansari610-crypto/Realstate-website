@extends('layouts.admin.master')

@section('main-content')
  <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Leads</h4>
                <div class="page-title-right">
                    <a href="{{ route('org.leads.create') }}" class="btn btn-primary">Add Lead</a>
                </div>
            </div>
        </div>
    </div>
<div class="row mt-2">

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                  <form class="row g-2 mb-3" method="get">
                    {{-- <div class="col-md-3">
                      <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search name/phone/email">
                    </div> --}}
                    <div class="col-md-2">
                      <label for="">Status</label>
                      <select name="status" class="form-select filter-select">
                        <option value="">All Statuses</option>
                        @foreach(['new','contacted','site_visit','booked','lost'] as $s)
                          <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label for="">Projects</label>
                      <select name="project_id" class="form-select filter-select">
                        <option value="">All Projects</option>
                        @foreach($projects as $p)
                          <option value="{{ $p->id }}" @selected(request('project_id')==$p->id)>{{ $p->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label for="">Assignes</label>
                      <select name="assigned_to" class="form-select filter-select">
                        <option value="">All Assignees</option>
                        @foreach($assignees as $a)
                          <option value="{{ $a->id }}" @selected(request('assigned_to')==$a->id)>{{ $a->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-2">
                      <label for="">Sources</label>
                      <select name="sources" class="form-select filter-select">
                        <option value="">All Sources</option>
                        @foreach($leadsSources as $a)
                          <option value="{{ $a }}" @selected(request('sources')==$a)>{{ $a }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-2 " style="margin-top: 38px;">
                      <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
                    </div>
                  </form>
                  <div class="table-responsive">
                  <table class="table table-striped align-middle" id="LeadTable" width="100%">
                    <thead>
                      <tr>
                        <th>SN</th>
                        <th>Lead</th>
                        <th>Contact</th>
                        <th>Project / Unit</th>
                        <th>Status</th>
                        <th>Follow-up</th>
                        <th>Source</th>
                        <th>Assignee</th>
                        <th class="text-end">Actions</th>
                      </tr>
                    </thead>
                    {{-- <tbody>
                      @forelse($leads as $l)
                        <tr>
                          <td class="fw-semibold">{{ $l->name }}</td>
                          <td>
                            <div>{{ $l->phone }}</div>
                            <div class="text-muted small">{{ $l->email }}</div>
                          </td>
                          <td>
                            <div>{{ $l->project?->name ?? '—' }}</div>
                            <div class="text-muted small">{{ $l->unit?->unit_no }}</div>
                          </td>
                          <td><span class="badge bg-secondary text-capitalize">{{ str_replace('_',' ',$l->status) }}</span></td>
                          <td>{{ $l->follow_up_at ? $l->follow_up_at->format('d M Y H:i') : '—' }}</td>
                          <td>{{ $l->assignee?->name ?? '—' }}</td>
                          <td class="text-end">
                            <a href="{{ route('org.leads.edit',$l) }}" class="btn btn-sm btn-outline-secondary">Edit</a>

                            @role('org admin')
                              <a href="{{ route('org.leads.assign',$l) }}" class="btn btn-sm btn-outline-primary">Assign</a>
                              <a href="{{ route('org.leads.materialise',$l) }}" class="btn btn-sm btn-outline-success">Materialise</a>
                            @endrole
                            @if(!auth()->user()->hasAnyRole(['channel partner']))
                            <form action="{{ route('org.leads.destroy',$l) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this lead?')">
                              @csrf @method('DELETE')
                              <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                            @endif
                          </td>
                        </tr>
                      @empty
                        <tr><td colspan="7">No leads found.</td></tr>
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
    let table = $('#LeadTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('org.leads.get') }}",
            data: function (d) {
                // d.q = $('input[name="q"]').val();
                d.status = $('select[name="status"]').val();
                d.project_id = $('select[name="project_id"]').val();
                d.assigned_to = $('select[name="assigned_to"]').val();
                d.source = $('select[name="sources"]').val();

            }
        },
        columns: [
            { data: 'sn', name: 'sn', orderable: false } ,
            { data: 'lead', name: 'leads.name' },
            { data: 'contact', name: 'leads.phone' },
            { data: 'project', name: 'projects.name' },
            { data: 'status', name: 'leads.status' },
            { data: 'follow_up', name: 'leads.follow_up_at' },
            { data: 'source', name: 'leads.source' },
            { data: 'assignee', name: 'assignees.name' },
            { data: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],  
        pageLength: 10
    });
 
    $('button[type="submit"]').click(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });
});
</script>
@endsection