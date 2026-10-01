  @extends('layouts.admin.master')

  @section('main-content')
  <div class="row">
      <div class="col-12">
          <div class="page-title-box d-sm-flex align-items-center justify-content-between">
              <h4 class="mb-sm-0">Add Partner</h4>
              <div class="page-title-right">
                  <a href="{{ route('org.partners.create') }}" class="btn btn-primary">Add Partner</a>
              </div>
          </div>
      </div>
  </div>
  <div class="row mt-2">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    {{-- <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="page-title">Partners</h3>
      <a href="{{ route('org.partners.create') }}" class="btn btn-primary">Add Partner</a>
    </div> --}}

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form class="row g-2 mb-3" method="get">
                  {{-- <div class="col-md-3"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search name/email/phone"></div> --}}
                  <div class="col-md-4">
                    <select name="type" class="form-select">
                      <option value="">All Types</option>
                      @foreach(['channel_partner'=>'Channel Partner','broker'=>'Broker','agent'=>'Agent'] as $k=>$v)
                        <option value="{{ $k }}" @selected(request('type')===$k)>{{ $v }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2"><button class="btn btn-outline-secondary w-100">Filter</button></div>
                </form>
                <div class="table-responsive">
                <table class="table table-striped align-middle" id="partner-table" width="100%">
                  <thead><tr>
                    <th>Sn</th><th>Name</th><th>Type</th><th>Phone</th><th>Email</th><th>Parent CP</th><th>Approved</th><th>Active</th><th class="text-end">Actions</th>
                  </tr></thead>
                  <tbody>
                    {{-- @forelse($partners as $p)
                      <tr>
                        <td class="fw-semibold">{{ $p->name }}</td>
                        <td class="text-capitalize">{{ str_replace('_',' ',$p->type) }}</td>
                        <td>{{ $p->phone }}</td>
                        <td>{{ $p->email }}</td>
                        <td>{{ $p->parent?->name ?? '—' }}</td>
                        <td>{!! $p->approved ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning text-dark">No</span>' !!}</td>
                        <td>{!! $p->active ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                        <td class="text-end">
                          <a href="{{route('partner-reset-password',Crypt::encrypt($p->id))}}" class="btn btn-sm btn-outline-warning reset-password">Reset Password</a>
                          <a href="{{ route('org.partners.edit',$p) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                          <a href="{{ route('org.partners.commissions.index',$p) }}" class="btn btn-sm btn-outline-info">Commissions</a>
                          <form action="{{ route('org.partners.destroy',$p) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this partner?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                          </form>
                        </td>
                      </tr>
                    @empty
                      <tr><td colspan="8">No partners found.</td></tr>
                    @endforelse --}}
                  </tbody>
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
   
    var table = $('#partner-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('org.partners.get') }}",
        data: function(d) {
            d.q = $('input[name="q"]').val();
            d.type = $('select[name="type"]').val();
        }
    },
    columns: [
        { data: 'sn', name: 'id',  searchable: false },
        { data: 'name', name: 'name' },
        { data: 'type', name: 'type' },
        { data: 'phone', name: 'phone' },
        { data: 'email', name: 'email' },
        { data: 'parent', name: 'parent_id', orderable: false, searchable: false },
        { data: 'approved', name: 'approved', orderable: false, searchable: false },
        { data: 'active', name: 'active', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
    ],
    order: [[0, 'desc']],  
    lengthMenu: [10, 20, 50]
});

    // Filter form submit
    $('form').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });
});
</script>
@endsection
