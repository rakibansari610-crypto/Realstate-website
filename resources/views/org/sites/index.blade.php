@extends('layouts.admin.master')
@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Sites</h4>
                <div class="page-title-right">
                    <a href="{{ route('org.sites.create') }}" class="btn btn-primary">Add
                        Site</a>
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
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Org</th>
                                    <th>Code</th>
                                    <th>Timezone</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sites as $s)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $s->name }}</td>
                                        <td>{{ $s->org?->name }}</td>
                                        <td>{{ $s->code ?? '—' }}</td>
                                        <td>{{ $s->timezone ?? 'UTC' }}</td>
                                        <td>{{ $s->created_at?->format('Y-m-d') }}</td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('org.sites.edit', $s->id) }}">Edit</a>
                                            <form class="d-inline" method="post"
                                                action="{{ route('org.sites.destroy', $s->id) }}"
                                                onsubmit="return confirm('Delete this site?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No sites found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $sites->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
