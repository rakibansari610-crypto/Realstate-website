@extends('layouts.admin.master')
@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Visit Approval</h4>
                <div class="page-title-right">
                    <a href="@role('receptionist'){{ route('receptionist.dashboard') }}@endrole" class="btn btn-primary">Home</a>
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
                                    <th>Visitor</th>
                                    <th>Site</th>
                                    <th>Purpose</th>
                                    <th>When</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $v)
                                    <tr>
                                        <td>{{ $v->visitor->name }} <small
                                                class="text-muted">{{ $v->visitor->phone ?? $v->visitor->email }}</small>
                                        </td>
                                        <td>{{ $v->site?->name }}</td>
                                        <td>{{ $v->purpose ?? '—' }}</td>
                                        <td>{{ $v->scheduled_start?->format('Y-m-d H:i') ?? '—' }}</td>
                                        <td class="text-end">
                                            @if ($v->status === 'approved')
                                             <a href="{{ route('org.visits.show', $v->id) }}"
                                                class="btn btn-sm btn-outline-secondary">View</a>
                                                <form class="d-inline" method="post" action="{{ route('org.visits.checkin', $v->id) }}">
                                                    @csrf <button class="btn btn-sm btn-primary">Check-In</button>
                                                </form>
                                            @endif
                                            @unlessrole('receptionist')
                                            <form class="d-inline" method="post"
                                                action="{{ route('org.visits.approve', $v->id) }}">
                                                @csrf <button class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <form class="d-inline" method="post"
                                                action="{{ route('org.visits.deny', $v->id) }}">
                                                @csrf <button class="btn btn-sm btn-danger">Deny</button>
                                            </form>
                                            @endunlessrole
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No pending approvals.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $visits->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
