@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="page-title">Countries</h3>
    <a href="{{ route('org.country.create') }}" class="btn btn-primary">Add Country</a>
  </div>

  <form class="mb-3" method="get">
    <div class="input-group">
      <input class="form-control" name="q" value="{{ request('q') }}"
             placeholder="Search Country...">
      <button class="btn btn-outline-secondary">Search</button>
    </div>
  </form>

  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th>#</th>
        <th>Country Name</th>
        <th>Code</th>
        <th>ISD</th>
        <th>Priority</th>
        <th>Status</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @php $i = 1; @endphp
      @forelse($countries as $ct)
        <tr>
          <td>{{ $i++ }}</td>
          <td>{{ $ct->country_name }}</td>
          <td>{{ $ct->country_code }}</td>
          <td>{{ $ct->country_isd }}</td>
          <td>{{ $ct->country_priority }}</td>
          <td>
            {!! $ct->country_status == 1
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>' !!}
          </td>
          <td class="text-end">
            <a href="{{ route('org.country.edit', encrypt($ct->id)) }}"
               class="btn btn-sm btn-outline-secondary">Edit</a>

            <form action="{{ route('org.country.destroy', encrypt($ct->id)) }}"
                  method="post" class="d-inline"
                  onsubmit="return confirm('Delete this country?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center">No countries found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{ $countries->links() }}
</div>
@endsection
