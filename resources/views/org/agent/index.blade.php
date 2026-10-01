  @extends('layouts.admin.master')

  @section('main-content')
  <div class="row">
      <div class="col-12">
          <div class="page-title-box d-sm-flex align-items-center justify-content-between">
              <h4 class="mb-sm-0">Agent List</h4>
              <div class="page-title-right">
                  <a href="{{ route('org.agent-create') }}" class="btn btn-primary">Add Agent</a>
              </div>
          </div>
      </div>
  </div>
  <div class="row mt-2">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
 
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                 
                <div class="table-responsive">
                <table class="table table-striped align-middle" id="agent-table" width="100%">
                    <thead>
                        <tr>
                            <th>Sn</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Facebook</th>
                            <th>Linkedin</th>
                            <th>Twitter</th> 
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                  <tbody>
                     
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
   
    var table = $('#agent-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('org.agent-get') }}",
        data: function(d) {
            d.q = $('input[name="q"]').val();
            d.type = $('select[name="type"]').val();
        }
    },
    columns: [
        { data: 'sn', name: 'id',  searchable: false },
        { data: 'image', name: 'image', searchable:false, orderable: false},
        { data: 'name', name: 'name' },
        { data: 'phone', name: 'phone' },
        { data: 'email', name: 'email' },
        { data: 'facebook', name: 'facebook', orderable: false },
        { data: 'twitter', name: 'twitter', orderable: false },
        { data: 'linkedin', name: 'linkedin', orderable: false }, 
        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
    ],
    order: [[0, 'desc']],  
    lengthMenu: [10, 20, 50]
});
 
});
</script>
@endsection
