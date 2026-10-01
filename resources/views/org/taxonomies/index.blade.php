@extends('layouts.admin.master')

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ ucfirst($module) }}  Categories</h4>
                <div class="page-title-right">
                   <a href="{{ route('taxonomies.create', $module) }}" class="btn btn-primary mb-3">Add New</a>
                </div>
            </div>
        </div>
    </div>
{{-- <h3>{{ ucfirst($module) }} Categories</h3> --}}
{{-- <a href="{{ route('taxonomies.create', $module) }}" class="btn btn-primary mb-3">Add New</a> --}}
    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                    <div class="table-responsive">
                        @if ($taxonomies->count())
                        <table class="table table-bordered" id="taxonomyTable" width="100%">
                            <thead>
                                <tr>
                                    <th>Parent</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            {{-- <tbody>
                                @foreach($taxonomies as $category)
                                    <tr>
                                        <td>{{ $category->parent ? $category->parent->name : '-' }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->priority }}</td>
                                        <td>{{ ucfirst($category->status) }}</td>
                                        <td>
                                            <a href="{{ route('taxonomies.edit', [$module, $category]) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('taxonomies.destroy', [$module, $category]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure to delete this category?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr> 
                                    @if($category->children && $category->children->count())
                                        @foreach($category->children as $child)
                                        <tr>
                                            <td>{{ $category->name }}</td>
                                            <td>{{ $child->name }}</td>
                                            <td>{{ $child->priority }}</td>
                                            <td>{{ ucfirst($child->status) }}</td>
                                            <td>
                                                <a href="{{ route('taxonomies.edit', [$module, $child]) }}" class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('taxonomies.destroy', [$module, $child]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure to delete this category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody> --}}
                        </table>
                        @else
                        <p>No categories found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
<script>
$(document).ready(function () {
    $('#taxonomyTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("taxonomies.get", $module) }}',
        columns: [
            { data: 'parent', name: 'parent.name' },
            { data: 'name', name: 'taxonomies.name' },
            { data: 'priority', name: 'taxonomies.priority' },
            { data: 'status', name: 'taxonomies.status' },
            { data: 'action', orderable: false, searchable: false }
        ],
        order: [[2, 'asc']]
    });
});
</script>

@endsection
