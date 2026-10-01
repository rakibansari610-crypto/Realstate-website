@extends('layouts.admin.master')
@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Users</h4>
                <div class="page-title-right">
                    <a href="{{ route('org.users.create') }}" class="btn btn-primary">Add
                        Users</a>
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
                        <table id="example" class="table table-striped nowrap align-middle table-colored"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Site</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $u)
                                    <tr>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td>{{ $u->site?->name ?? '—' }}</td>
                                        {{-- <td>{{ $u->roles->pluck('name')->implode(', ') }}</td> --}}
                                        <td>{{ $u->roles->pluck('name')
                                             ->map(function($role){ return $role === 'rm' ? 'Relationship Manager' : ucfirst($role);
                                             }) ->implode(', ')  }}</td>
                                        <td>{{ $u->created_at?->format('Y-m-d') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('org.users.edit', $u->id) }}"
                                                class="btn btn-sm btn-outline-primary">Edit</a>
                                            <form method="post" action="{{ route('org.users.destroy', $u->id) }}"
                                                class="d-inline" onsubmit="return confirm('Delete user?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
