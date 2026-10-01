@extends('layouts.admin.master')
@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Visits</h4>
                <div class="page-title-right">
                    @role('receptionist')
                    <a href="@role('receptionist'){{ route('receptionist.dashboard') }}@endrole" class="btn btn-primary">Home</a>
                    @endrole
                    @role('employee')
                    <a href="{{ route('org.visits.prebook') }}" class="btn btn-primary">New Visit</a>
                    @endrole
                    
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
                                    <th>#</th>
                                    <th>Visitor</th>
                                    <th>Host</th>
                                    <th>Site</th>
                                    <th>Purpose</th>
                                    <th>When</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $v)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $v->visitor?->name }}</div>
                                            <small class="text-muted">
                                                {{ $v->visitor?->phone ?? ($v->visitor?->email ?? '—') }}
                                            </small>
                                        </td>
                                        <td>{{ $v->host?->name ?? '—' }}</td>
                                        <td>{{ $v->site?->name ?? '—' }}</td>
                                        <td>{{ $v->purpose ?? '—' }}</td>
                                        <td>
                                            @if ($v->scheduled_start)
                                                <div>{{ $v->scheduled_start->format('d/m/Y H:i') }}</div>
                                            @endif
                                            @if ($v->scheduled_end)
                                                <small class="text-muted">→
                                                    {{ $v->scheduled_end->format('d/m/Y H:i') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $badge = match ($v->status) {
                                                    'pending' => 'bg-warning',
                                                    'approved' => 'bg-info',
                                                    'denied' => 'bg-danger',
                                                    'checked_in' => 'bg-primary',
                                                    'checked_out' => 'bg-success',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span
                                                class="badge {{ $badge }}">{{ str_replace('_', ' ', ucfirst($v->status)) }}</span>
                                        </td>
                                        <td class="text-end">
                                            {{-- View --}}
                                            <a href="{{ route('org.visits.show', $v->id) }}"
                                                class="btn btn-sm btn-outline-secondary">View</a>

                                            {{-- Host approval (only if you are the host and status is pending) --}}
                                            @if (auth()->id() === $v->host_user_id && $v->status === 'pending')
                                                <form class="d-inline" method="post"
                                                    action="{{ route('org.visits.approve', $v->id) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success">Approve</button>
                                                </form>
                                                <form class="d-inline" method="post"
                                                    action="{{ route('org.visits.deny', $v->id) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-danger">Deny</button>
                                                </form>
                                            @endif

                                            {{-- Reception/org/site admin check-in/out --}}
                                            @role('org admin|site admin|receptionist')
                                                @if ($v->status === 'approved')
                                                    <form class="d-inline" method="post"
                                                        action="{{ route('org.visits.checkin', $v->id) }}">
                                                        @csrf
                                                        <button class="btn btn-sm btn-primary">Check-In</button>
                                                    </form>
                                                @endif
                                                @if ($v->status === 'checked_in')
                                                    <form class="d-inline" method="post"
                                                        action="{{ route('org.visits.checkout', $v->id) }}">
                                                        @csrf
                                                        <button class="btn btn-sm btn-success">Check-Out</button>
                                                    </form>
                                                @endif
                                            @endrole
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No visits found.</td>
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
