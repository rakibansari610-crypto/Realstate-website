@extends('layouts.app')
@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h3 class="mb-1">{{ $project['name'] ?? 'Project' }}</h3>
            <div class="text-muted" style="font-size:13px;">
                Developer: <strong>{{ $project['developer'] ?? '-' }}</strong>
                • ID: <strong>{{ $project['id'] ?? '-' }}</strong>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ url('reelly_properties') }}" class="btn btn-outline-secondary btn-sm">← Back</a>

            @if(!empty($project['id']))
                <a href="{{ url('reelly/projects/' . $project['id'] . '/units') }}" class="btn btn-outline-success btn-sm">
                    View Units
                </a>
            @endif
        </div>
    </div>

    {{-- Cover Image --}}
    @if(!empty($project['cover_image']['url']))
        <div class="card shadow-sm mb-3">
            <img src="{{ $project['cover_image']['url'] }}"
                 alt="Cover"
                 class="img-fluid"
                 style="max-height:360px; width:100%; object-fit:cover;">
        </div>
    @endif

    <div class="row g-3">

        {{-- Left: Key Facts --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">Key Facts</div>
                <div class="card-body">

                    @php
                        $loc = $project['location'] ?? [];
                        $sale = $project['sale_status'] ?? '-';
                        $construction = $project['construction_status'] ?? '-';
                    @endphp

                    <div class="mb-2">
                        <div class="text-muted" style="font-size:12px;">Construction Status</div>
                        <span class="badge bg-secondary">{{ $construction }}</span>
                    </div>

                    <div class="mb-2">
                        <div class="text-muted" style="font-size:12px;">Sale Status</div>
                        <span class="badge {{ $sale === 'on_sale' ? 'bg-success' : 'bg-danger' }}">{{ $sale }}</span>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <div class="text-muted" style="font-size:12px;">Completion</div>
                        <div class="fw-semibold">{{ $project['completion_date'] ?? '-' }}</div>
                        <div class="text-muted" style="font-size:12px;">
                            {{ $project['completion_datetime'] ?? '' }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="text-muted" style="font-size:12px;">Construction Period</div>
                        <div>
                            <span class="fw-semibold">{{ $project['construction_start_date'] ?? '-' }}</span>
                            —
                            <span class="fw-semibold">{{ $project['construction_end_date'] ?? '-' }}</span>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <div class="text-muted" style="font-size:12px;">Buildings</div>
                        <div class="fw-semibold">{{ $project['building_count'] ?? 0 }}</div>
                    </div>

                    <div class="mb-2">
                        <div class="text-muted" style="font-size:12px;">Units Count</div>
                        <div class="fw-semibold">{{ $project['units_count'] ?? 0 }}</div>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <div class="text-muted" style="font-size:12px;">Location</div>
                        <div class="fw-semibold">
                            {{ $loc['district'] ?? '-' }},
                            {{ $loc['region'] ?? '-' }}
                        </div>
                        <div class="text-muted" style="font-size:12px;">
                            Lat: {{ $loc['latitude'] ?? '-' }},
                            Lng: {{ $loc['longitude'] ?? '-' }}
                        </div>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <div class="text-muted" style="font-size:12px;">Price Range ({{ $project['price_currency'] ?? 'AED' }})</div>
                        <div class="fw-semibold">
                            {{ !empty($project['min_price']) ? number_format((float)$project['min_price'], 2) : '-' }}
                            —
                            {{ !empty($project['max_price']) ? number_format((float)$project['max_price'], 2) : '-' }}
                        </div>
                    </div>

                    <div class="mb-0">
                        <div class="text-muted" style="font-size:12px;">Area Range ({{ $project['area_unit'] ?? 'sqft' }})</div>
                        <div class="fw-semibold">
                            {{ !empty($project['min_size']) ? number_format((float)$project['min_size'], 2) : '-' }}
                            —
                            {{ !empty($project['max_size']) ? number_format((float)$project['max_size'], 2) : '-' }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Right: Overview --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">Overview</div>
                <div class="card-body">

                    @php
                        // The API overview is markdown-ish. We'll show it safely.
                        // If you want markdown rendering, tell me and I’ll add a parser.
                        $overview = $project['overview'] ?? '';
                        $overview = trim($overview);
                    @endphp

                    @if($overview)
                        <div style="white-space:pre-wrap; line-height:1.6;">
                            {{ $overview }}
                        </div>
                    @else
                        <div class="text-muted">No overview available.</div>
                    @endif

                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white fw-semibold">Meta</div>
                <div class="card-body">
                    <div class="text-muted" style="font-size:13px;">
                        Last updated: <strong>{{ $project['updated_at'] ?? '-' }}</strong>
                        • Published: <strong>{{ !empty($project['is_published']) ? 'Yes' : 'No' }}</strong>
                        • Partner Project: <strong>{{ !empty($project['is_partner_project']) ? 'Yes' : 'No' }}</strong>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

@endsection
