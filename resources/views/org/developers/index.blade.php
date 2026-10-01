@extends('layouts.admin.master')

@section('main-content')
  <div class="row">
      <div class="col-12">
          <div class="page-title-box d-sm-flex align-items-center justify-content-between">
              <h4 class="mb-sm-0">Developers</h4>
              <div class="page-title-right">
                   @if(!auth()->user()->hasAnyRole(['channel partner']))
                    <a href="{{ route('org.developers.create') }}" class="btn btn-primary">Add Developer</a>
                    @endif
              </div>
          </div>
      </div>
  </div>
  <div class="row mt-2">
    <div class="col-lg-12 ">
        <div class="card">
          {{-- <form class="mb-3" method="get">
            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search developers...">
          </form> --}}
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped nowrap align-middle table-colored" style="width:100%" id="developers-table">
                <thead>
                  <tr>
                    <th style="width: 20px;">Sn</th>
                    <th>Name</th>
                    <th>Website</th>
                    <th>Logo</th>
                    @hasanyrole('org admin|admin')
                    <th>Created By</th>
                    @endrole
                    <th class="text-end">Actions</th> 
                  </tr>
                </thead>
                {{-- <tbody>
                @forelse($developers as $d)
                  <tr>
                    <td>{{ $d->name }}</td>
                    <td><a href="{{ $d->website }}" target="_blank">{{ $d->website }}</a></td>
                    <td>@if($d->logo_path)<img src="{{ Storage::disk('public')->url($d->logo_path) }}" style="height:36px">@endif</td>
                    @hasanyrole('org admin|admin')
                    <td>{{ $d->creator->name ?? '—' }}</td>
                    @endrole
                    <td class="text-end">
                      @if(!auth()->user()->hasAnyRole(['channel partner']))
                      <a href="{{ route('org.developers.edit',$d) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                      @endif
                      <a href="{{ route('org.developers.view',$d) }}" class="btn btn-sm btn-outline-success">View</a>
                      @if(!auth()->user()->hasAnyRole(['data entry', 'channel partner']))
                      <form action="{{ route('org.developers.destroy',$d) }}" method="post" class="d-inline"
                            onsubmit="return confirm('Delete this developer?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                      </form>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="4">No developers yet.</td></tr>
                @endforelse
                </tbody> --}}
              </table>
            </div>
          </div>
          {{-- {{ $developers->links() }} --}}
        </div>
    </div>
  </div>
@endsection
@section('scripts')
<script>
$(document).ready(function() {
    $('#developers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('org.developers.get') }}",
        columns: [
            { data: 'sn', name: 'id' }, 
            { data: 'name', name: 'name' },
            { data: 'website', name: 'website' },
            { data: 'logo', name: 'logo', orderable: false, searchable: false },
            @hasanyrole('org admin|admin')
            { data: 'creator', name: 'creator_name' },
            @endrole
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
        ]
    });
});
</script>

@endsection