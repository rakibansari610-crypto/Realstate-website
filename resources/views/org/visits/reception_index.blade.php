@extends('layouts.admin.master')
@section('main-content')
    <div class="vs-reception">
        <!-- gradient & blobs live in CSS :before/:after -->
        <div class="vs-reception-header">
            <div class="vs-greeting">
                <h1>Good {{ now()->format('H') < 12 ? 'Morning' : (now()->format('H') < 18 ? 'Afternoon' : 'Evening') }}
                    <span>👋</span>
                </h1>
                <h2>{{ Auth::user()->name }}</h2>
            </div>

            <div class="vs-user-meta d-flex align-items-center gap-3">
                <!-- Clock -->
                <div id="live-clock" class="fw-bold text-white" style="font-size: 32px;"></div>

                @role('receptionist')
                    <a title="logout" class="btn btn-danger" style="padding: 5px 10px;" href="{{ route('receptionist.dashboard') }}">
                        <i class="las la-home" style="font-size: 34px;"></i>
                    </a>
                @endrole
            </div>
        </div>
        <div class="vs-panels" style="grid-template-columns:none">
            <div class="vs-panel">
                <div class="vs-panel-title">Visitors</div>
                <div class="table-responsive">
                    <table id="example" class="table table-striped nowrap align-middle table-colored" style="width:100%">
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
@endsection
