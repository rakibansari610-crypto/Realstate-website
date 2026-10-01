@extends('layouts.admin.master')
@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Organisations</h4>
                <div class="page-title-right">
                    <a href="{{ route('su.org.create') }}" class="btn btn-primary">Add
                        Organisation</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">


                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif



                    <div class="table-responsive">
                        <table id="organization-table" class="table table-striped nowrap align-middle table-colored"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name / Slug</th>
                                    <th>Subdomain</th>
                                    <th>Custom Domain</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orgs as $org)
                                    <tr>
                                        <td>{{ $org->id }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $org->name }}</div>
                                            <small class="text-muted">{{ $org->slug }}</small>
                                        </td>
                                        <td>{{ $org->subdomain }}</td>
                                        <td>{{ $org->custom_domain ?? '—' }}</td>
                                        <td>{{ $org->created_at?->format('Y-m-d') }}</td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('su.org.edit', $org->id) }}">Edit</a>
                                            <a class="btn btn-sm btn-outline-secondary"
                                                href="{{ route('su.org.branding.edit', $org->id) }}">Branding</a>

                                            @php
                                                $tenantBase = config('tenancy.base_domain');
                                                $tenantUrl = $org->custom_domain
                                                    ? request()->getScheme() . '://' . $org->custom_domain
                                                    : request()->getScheme() .
                                                        '://' .
                                                        $org->subdomain .
                                                        '.' .
                                                        $tenantBase;
                                            @endphp
                                            <a class="btn btn-sm btn-light" target="_blank" rel="noopener"
                                                href="{{ $tenantUrl }}/login">Open Tenant</a>


                                            <form action="{{ route('su.org.destroy', $org->id) }}" method="post"
                                                class="d-inline" onsubmit="return confirm('Delete this organization?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No organizations found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $orgs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
$(document).ready(function() {
    $('#organization-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('su.org.get') }}",  
        columns: [
            { data: 0, name: 'id' },
            { data: 1, name: 'name' },
            { data: 2, name: 'subdomain' },
            { data: 3, name: 'custom_domain' },
            { data: 4, name: 'created_at' },
            { data: 5, name: 'actions', orderable: false, searchable: false,className: 'text-end' }
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endsection
