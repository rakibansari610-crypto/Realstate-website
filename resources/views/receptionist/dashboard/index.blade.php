@extends('layouts.admin.master')

@section('main-content')
    @php
        // Provide your real numbers here (from controller)
        $todayVisits = $todayVisits ?? 140;
        $newRequests = $newRequests ?? 20;
        $approvedVisits = $approvedVisits ?? 120;

        // Example activity/approvals (replace with real data)
        $recent = $recent ?? [
            ['text' => 'Visitor John completed his form', 'time' => 'Yesterday at 11:45 AM'],
            ['text' => 'Visitor Mary checked in', 'time' => 'Yesterday at 10:20 AM'],
            ['text' => 'Pass #VS-214 scanned', 'time' => 'Yesterday at 09:00 AM'],
        ];

        $approvals = $approvals ?? [
            ['text' => 'Meeting with Kelly', 'time' => '9:00 AM'],
            ['text' => 'Interview with James', 'time' => '11:00 AM'],
            ['text' => 'Delivery from Amazon', 'time' => '3:30 PM'],
        ];
    @endphp

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
                    <a title="logout" class="btn btn-danger" style="padding: 5px 10px;" href="{{ url('logout') }}">
                        <i class="las la-sign-out-alt" style="font-size: 34px;"></i>
                    </a>
                @endrole
            </div>
        </div>
        
        <!-- KPI row -->
        <div class="vs-kpi-grid">
            <div class="vs-kpi vs-kpi-blue">
                <div class="vs-kpi-value">{{ $todayVisits }}</div>
                <div class="vs-kpi-label">Today’s Visits</div>
            </div>
            <div class="vs-kpi vs-kpi-mint">
                <div class="vs-kpi-value">{{ $newRequests }}</div>
                <div class="vs-kpi-label">New Requests</div>
            </div>
            <div class="vs-kpi vs-kpi-warm">
                <div class="vs-kpi-value">{{ $approvedVisits }}</div>
                <div class="vs-kpi-label">Approved Visits</div>
            </div>
        </div>
            

        <!-- Actions row -->
        <div class="vs-actions">
            <a class="vs-action vs-action-scan" href="{{ route('org.visits.scan.camera') }}">
                <div class="vs-action-icon">🧭</div>
                <div>
                    <div class="vs-action-title">Scan QR Code</div>
                    <div class="vs-action-sub">Check account</div>
                </div>
            </a>

            <a class="vs-action vs-action-register" href="{{ route('org.visits.create') }}">
                <div class="vs-action-icon">➕</div>
                <div>
                    <div class="vs-action-title">Register Visitor</div>
                    <div class="vs-action-sub">3-step wizard</div>
                </div>
            </a>

            <a class="vs-action vs-action-visits" href="{{ route('org.visits.index') }}">
                <div class="vs-action-icon">📘</div>
                <div>
                    <div class="vs-action-title">View All Visits</div>
                    <div class="vs-action-sub">Search & manage</div>
                </div>
            </a>
        </div>

        <!-- Lower panels -->
        <div class="vs-panels">
            <div class="vs-panel">
                <div class="vs-panel-title">Recent Activity</div>
                <ul class="vs-activity">
                    @foreach ($recent as $row)
                        <li>
                            <span class="dot"></span>
                            <span class="text">{{ $row['text'] }}</span>
                            <span class="time">{{ $row['time'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="vs-panel">
                <div class="vs-panel-title">Pending Approvals</div>
                <ul class="vs-approvals">
                    @foreach ($approvals as $row)
                        <li>
                            <span class="hourglass">⌛</span>
                            <span class="text">{{ $row['text'] }}</span>
                            <span class="time">{{ $row['time'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    
@endsection
