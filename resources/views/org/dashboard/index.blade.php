{{-- resources/views/org/dashboard.blade.php --}}
@extends('layouts.admin.master')

@section('main-content')
<style>
  /* quick, minimal theming */
  .hero {
    background: linear-gradient(135deg,#0ea5e9 0%,#7c3aed 100%);
    border: 0; color: #fff; border-radius: 14px;
    box-shadow: 0 10px 24px rgba(124,58,237,.25);
  }
  .hero .title { font-weight: 700; letter-spacing: .2px; }
  .kpi { border:0;border-radius:14px;color:#0b1220; box-shadow:0 6px 16px rgba(2,6,23,.06); }
  .kpi .badge { font-weight:600 }
  .kpi-blue   { background: linear-gradient(135deg,#dbeafe,#bfdbfe); }
  .kpi-green  { background: linear-gradient(135deg,#dcfce7,#bbf7d0); }
  .kpi-amber  { background: linear-gradient(135deg,#fef3c7,#fde68a); }
  .kpi-purple { background: linear-gradient(135deg,#ede9fe,#ddd6fe); }
  .kpi-slate  { background: linear-gradient(135deg,#e5e7eb,#e2e8f0); }
  .card-shadow { box-shadow:0 6px 16px rgba(2,6,23,.06); border:0; border-radius:14px; }
</style>

<div class="content container-fluid">

  {{-- HERO / GREETING --}}
  <div class="card hero mb-4">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
      <div class="mb-2">
        <div class="title h4 mb-1">{{ $greeting }}</div>
        <div class="opacity-75">Here’s what’s happening across your organization today.</div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('org.leads.create') }}" class="btn btn-light text-primary fw-semibold">+ New Lead</a>
        <a href="{{ route('org.projects.index') }}" class="btn btn-outline-light text-white">View Projects</a>
      </div>
    </div>
  </div>

  {{-- KPIs --}}
  <div class="row g-3">
    <div class="col-md-2">
      <div class="card kpi kpi-blue">
        <div class="card-body">
          <div class="small text-muted">Leads</div>
          <div class="display-6 fw-bold">{{ $leadsTotal }}</div>
          <span class="badge bg-success-subtle text-success">Booked: {{ $leadsBooked }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card kpi kpi-purple">
        <div class="card-body">
          <div class="small text-muted">Projects</div>
          <div class="display-6 fw-bold">{{ $projectsCount }}</div>
          <span class="badge bg-primary-subtle text-primary">Developers: {{ $developersCount }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card kpi kpi-slate">
        <div class="card-body">
          <div class="small text-muted">Partners</div>
          <div class="display-6 fw-bold">{{ $partnersCount }}</div>
          <span class="badge bg-dark-subtle text-dark">Active</span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card kpi kpi-green">
        <div class="card-body">
          <div class="small text-muted">Units</div>
          <div class="display-6 fw-bold">{{ $unitsTotal }}</div>
          <div class="small">
            <span class="text-success fw-semibold">Available: {{ $unitsAvailable }}</span>
            &nbsp;|&nbsp;
            <span class="text-danger">Booked: {{ $unitsBooked }}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card kpi kpi-amber">
        <div class="card-body">
          <div class="small text-muted">30-day Conversion</div>
          <div class="display-6 fw-bold {{ $conversion30>=30 ? 'text-success' : 'text-danger' }}">{{ $conversion30 }}%</div>
          <span class="small opacity-75">{{ $conversion30>=30 ? 'Great momentum' : 'Room to improve' }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- CHARTS ROW --}}
  <div class="row g-3 mt-1">
    <div class="col-lg-4">
      <div class="card card-shadow h-100">
        <div class="card-header fw-semibold">Lead Status</div>
        <div class="card-body"><div id="leadStatusPie"></div></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card card-shadow h-100">
        <div class="card-header fw-semibold">Leads (Last 12 Weeks)</div>
        <div class="card-body"><div id="leadTrendLine"></div></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card card-shadow h-100">
        <div class="card-header fw-semibold">Project Inventory</div>
        <div class="card-body"><div id="projectInvStacked"></div></div>
      </div>
    </div>
  </div>

  {{-- LISTS ROW --}}
  <div class="row g-3 mt-1">
    <div class="col-lg-4">
      <div class="card card-shadow h-100">
        <div class="card-header fw-semibold">Top Partners (by Leads)</div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            @forelse($topPartners as $tp)
              <li class="list-group-item d-flex justify-content-between">
                <span>{{ $tp->name }}</span>
                <span class="fw-semibold">{{ $tp->leads }}</span>
              </li>
            @empty
              <li class="list-group-item text-muted">No data</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card card-shadow h-100">
        <div class="card-header fw-semibold">Upcoming Follow-ups (7 days)</div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            @forelse($followUps as $f)
              <li class="list-group-item">
                <div class="fw-semibold">{{ $f->name }} <span class="text-muted small">({{ $f->phone }})</span></div>
                <div class="small text-muted">
                  {{ $f->follow_up_at?->format('d M Y, H:i') }}
                  @if($f->assignee) • {{ $f->assignee->name }} @endif
                </div>
              </li>
            @empty
              <li class="list-group-item text-muted">No upcoming follow-ups</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card card-shadow h-100">
        <div class="card-header fw-semibold">Recent Leads</div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead>
                <tr><th>Lead</th><th>Status</th><th>Project</th><th>Owner</th></tr>
              </thead>
              <tbody>
                @forelse($recentLeads as $l)
                  <tr>
                    <td class="fw-semibold">{{ $l->name }}</td>
                    <td class="text-capitalize">{{ str_replace('_',' ',$l->status) }}</td>
                    <td>{{ $l->project?->name ?? '—' }}</td>
                    <td>{{ $l->assignee?->name ?? '—' }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-muted">No leads</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3"></script>
<script>
  // Use palette from controller
  const PALETTE = @json($palette);

  // Lead Status Pie
  new ApexCharts(document.querySelector('#leadStatusPie'), {
    chart: { type:'pie', height:300 },
    labels: @json($leadStatus['labels']),
    series: @json($leadStatus['series']),
    colors: PALETTE,
    legend: { position:'bottom' }
  }).render();

  // Lead Trend Line
  new ApexCharts(document.querySelector('#leadTrendLine'), {
    chart: { type:'line', height:300, toolbar:{show:false} },
    xaxis: { categories: @json($leadTrend['labels']) },
    series: [{ name:'Leads', data:@json($leadTrend['series']) }],
    stroke: { curve:'smooth', width:4 },
    colors: [PALETTE[0]],
    markers: { size:3 },
    grid: { strokeDashArray:4 },
    dataLabels: { enabled:false }
  }).render();

  // Project Inventory Stacked
  new ApexCharts(document.querySelector('#projectInvStacked'), {
    chart: { type:'bar', stacked:true, height:300, toolbar:{show:false} },
    xaxis: { categories: @json($inv['labels']) },
    series: [
      { name:'Available', data:@json($inv['available']) },
      { name:'Booked',    data:@json($inv['booked']) }
    ],
    colors: [PALETTE[1], PALETTE[3]],
    plotOptions: { bar:{ borderRadius:6 } },
    legend: { position:'bottom' }
  }).render();
</script>
@endsection
