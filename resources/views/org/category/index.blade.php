@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="page-title"> Amenities Category</h3>
    <a href="{{ route('org.category.create') }}" class="btn btn-primary">Add Category</a>
  </div>

  <form class="mb-3" method="get">
    <div class="input-group">
      <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search Category...">
      <button class="btn btn-outline-secondary">Search</button>
    </div>
  </form>

  <table class="table table-striped align-middle">
    <thead>
      <tr>
         <th>S.no</th>
       
        <th>Category Name</th>
        <th>Icon</th>
        <th>Priority</th>
        <th>Status</th> 
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
       @php $i = 1; @endphp
      @forelse($category as $ct)
        <tr>
           <td>{{$i++}}</td>
          <td>{{ $ct->category_name }}</td>
          <td>{{ $ct->icon }}</td>
          <td>{{ $ct->priority }}</td> 
          <td>{!! $ct->status == 1 
              ? '<span class="badge bg-secondary text-capitalize">Active</span>' 
              : '<span class="badge bg-danger text-capitalize">Deactive</span>' 
          !!}</td>

          <td class="text-end">
            <a href="{{ route('org.category.edit',$ct) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            <form action="{{ route('org.category.destroy',$ct->id) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this project?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5">No Category found.</td></tr>
      @endforelse
    </tbody>
  </table>

  {{ $category->links() }}
</div>
@endsection
