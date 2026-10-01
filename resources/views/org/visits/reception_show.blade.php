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
                    <a title="logout" class="btn btn-danger" style="padding: 5px 10px;"
                        href="{{ route('receptionist.dashboard') }}">
                        <i class="las la-home" style="font-size: 34px;"></i>
                    </a>
                @endrole
            </div>
        </div>
        <div class="vs-panels" style="grid-template-columns:none">
            <div class="vs-panel">
               
                <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header fw-semibold">Visitor</div>
                                <div class="card-body">
                                    @if ($visit->visitor?->photo_path)
                                        <img src="{{ asset('storage/' . $visit->visitor->photo_path) }}" alt="Visitor Photo"
                                            class="rounded" style="width:120px;object-fit:cover;">
                                    @endif
                                    <div class="mb-1"><b>Name:</b> {{ $visit->visitor?->name }}</div>
                                    <div class="mb-1"><b>Phone:</b> {{ $visit->visitor?->phone ?? '—' }}</div>
                                    <div class="mb-1"><b>Email:</b> {{ $visit->visitor?->email ?? '—' }}</div>
                                    <div class="mb-1"><b>Company:</b> {{ $visit->visitor?->company ?? '—' }}</div>
                                    <div class="mb-1"><b>Vehicle:</b> {{ $visit->visitor?->vehicle_no ?? '—' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header fw-semibold">Visit</div>
                                <div class="card-body">

                                    <div class="mb-1"><b>Host:</b> {{ $visit->host?->name }} ({{ $visit->host?->email }})
                                    </div>
                                    <div class="mb-1"><b>Site:</b> {{ $visit->site?->name }}</div>
                                    <div class="mb-1"><b>Purpose:</b> {{ $visit->purpose ?? '—' }}</div>
                                    <div class="mb-1"><b>Scheduled:</b>
                                        {{ $visit->scheduled_start?->format('Y-m-d H:i') ?? '—' }}
                                        @if ($visit->scheduled_end)
                                            → {{ $visit->scheduled_end->format('Y-m-d H:i') }}
                                        @endif
                                    </div>
                                    <div class="mb-1">
                                        <b>Status:</b>
                                        <span
                                            class="badge bg-secondary">{{ str_replace('_', ' ', ucfirst($visit->status)) }}</span>
                                    </div>
                                    <div class="mb-1"><b>QR Token:</b> <code>{{ $visit->qr_token }}</code></div>
                                    @if ($visit->approved_at)
                                        <div class="mb-1"><b>Approved:</b> {{ $visit->approved_at->format('Y-m-d H:i') }}
                                        </div>
                                    @endif
                                    @if ($visit->denied_at)
                                        <div class="mb-1"><b>Denied:</b> {{ $visit->denied_at->format('Y-m-d H:i') }}
                                        </div>
                                    @endif
                                    @if ($visit->checked_in_at)
                                        <div class="mb-1"><b>Checked In:</b>
                                            {{ $visit->checked_in_at->format('Y-m-d H:i') }}</div>
                                    @endif
                                    @if ($visit->checked_out_at)
                                        <div class="mb-1"><b>Checked Out:</b>
                                            {{ $visit->checked_out_at->format('Y-m-d H:i') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        {{-- Host actions --}}
                        @if (auth()->id() === $visit->host_user_id && $visit->status === 'pending')
                            <form class="d-inline" method="post" action="{{ route('org.visits.approve', $visit->id) }}">
                                @csrf <button class="btn btn-success">Approve</button>
                            </form>
                            <form class="d-inline" method="post" action="{{ route('org.visits.deny', $visit->id) }}">
                                @csrf <button class="btn btn-danger">Deny</button>
                            </form>
                        @endif

                        {{-- Reception/org/site admin actions --}}
                        @role('org admin|site admin|receptionist')
                            @if ($visit->status === 'approved')
                                <form class="d-inline" method="post" action="{{ route('org.visits.checkin', $visit->id) }}">
                                    @csrf <button class="btn btn-primary">Check-In</button>
                                </form>
                            @endif

                            @if ($visit->status === 'checked_in')
                                <form class="d-inline" method="post" action="{{ route('org.visits.checkout', $visit->id) }}">
                                    @csrf <button class="btn btn-success">Check-Out</button>
                                </form>
                            @endif
                            <a href="{{ route('org.visits.pass', $visit->id) }}" target="_blank"
                                class="btn btn-outline-secondary">
                                Print Pass
                            </a>
                        @endrole
                    </div>

            </div>
        </div>
    </div>
    </div>
@endsection
