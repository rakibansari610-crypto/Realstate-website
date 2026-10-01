@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="page-title"> Amenities Sub Category</h3>
    <a href="{{ route('org.sub_category.create') }}" class="btn btn-primary">Add Sub Category</a>
  </div>

  {{-- <form class="mb-3" method="get">
    <div class="input-group">
      <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search sub catergory...">
      <button class="btn btn-outline-secondary">Search</button>
    </div>
  </form> --}}

  <table class="table table-striped align-middle" id="SubcategoryTable">
    <thead>
      <tr>
        <th>S.no</th>
        <th>Sub Category Name</th>
        <th>Category Name</th>
        <th>Icon</th>
        <th>Priority</th>
        <th>Status</th> 
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
          {{-- @php $i = 1; @endphp
        @forelse($sub_category as $ct)
          <tr>
            <td>{{$i++}}</td>
            <td>{{ $ct->sub_category_name }}</td>
            <td>{{ $ct->category_name }}</td>
            <td>{{ $ct->icon }}</td>
            <td>{{ $ct->priority }}</td>
            <td>{!! $ct->status == 1 
                ? '<span class="badge bg-secondary text-capitalize">Active</span>' 
                : '<span class="badge bg-danger text-capitalize">Deactive</span>' 
            !!}</td>
            
            <td class="text-end">
              <a href="{{ route('org.sub_category.edit',$ct) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
              <form action="{{ route('org.sub_category.destroy',$ct->id) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this project?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5">No Category found.</td></tr>
        @endforelse --}}
    </tbody>
  </table>

  {{ $sub_category->links() }}
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function() {
    $('#SubcategoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('org.subcategory.get') }}", // ✅ route for getSubCategories()
            type: 'GET'
        },
        columns: [
            { data: 'sn', name: 'sn', orderable: false },
            { data: 'sub_category_name', name: 'sub_category_name' },
            { data: 'category_name', name: 'category_name' },
            { 
                data: 'icon', 
                name: 'icon',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return data ? data : '<span class="text-muted">No Icon</span>';
                }
            },
            { data: 'priority', name: 'priority' },
            { 
                data: 'status',
                name: 'status',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return data;
                }
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false 
            }
        ],
        order: [[4, 'asc']],
        pageLength: 10
    });
});
</script>
@endsection

