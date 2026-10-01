@extends('layouts.app')
@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Reelly Projects</h3>
        <div class="text-muted" style="font-size:13px;">
            Total: <strong>{{ $data['count'] ?? 0 }}</strong>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
    <th style="width:70px;">ID</th>
    <th>Project</th>
    <th>Developer</th>
    <th>Construction</th>
    <th>Sale</th>
    <th>Completion</th>
    <th>District</th>
    <th>Region</th>
    <th class="text-end">Units</th>
    <th class="text-end">Min Price</th>
    <th class="text-end">Max Price</th>
    <th style="width:160px;">Actions</th>
</tr>
                    </thead>

                    <tbody>
                        @forelse(($data['results'] ?? []) as $p)
                            @php
                                $location = $p['location'] ?? [];
                                $district = $location['district'] ?? '-';
                                $region   = $location['region'] ?? '-';
                                $minPrice = $p['min_price'] ?? 0;
                                $maxPrice = $p['max_price'] ?? 0;
                            @endphp

                            <tr>
                                <td>{{ $p['id'] ?? '-' }}</td>

                                <td>
                                    <div class="fw-semibold">{{ $p['name'] ?? '-' }}</div>
                                    @if(!empty($p['cover_image']['url']))
                                        <a href="{{ $p['cover_image']['url'] }}" target="_blank" class="text-decoration-none" style="font-size:12px;">
                                            View cover
                                        </a>
                                    @endif
                                </td>

                                <td>{{ $p['developer'] ?? '-' }}</td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $p['construction_status'] ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    @php $sale = $p['sale_status'] ?? '-' @endphp
                                    <span class="badge {{ $sale === 'on_sale' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $sale }}
                                    </span>
                                </td>

                                <td>{{ $p['completion_date'] ?? '-' }}</td>
                                <td>{{ $district }}</td>
                                <td>{{ $region }}</td>

                                <td class="text-end">{{ $p['units_count'] ?? 0 }}</td>

                                <td class="text-end">
                                    {{ $minPrice > 0 ? number_format($minPrice, 2) : '-' }}
                                </td>

                                <td class="text-end">
                                    {{ $maxPrice > 0 ? number_format($maxPrice, 2) : '-' }}
                                </td>
                                <td>
    <div class="d-flex flex-column gap-1">
        <a href="{{ url('reelly/projects/' . $p['id']) }}"
           class="btn btn-outline-primary btn-sm">
            View Details
        </a>

        <a href="{{ url('reelly/projects/' . $p['id'] . '/units') }}"
           class="btn btn-outline-success btn-sm">
            View Units
        </a>
    </div>
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center p-4 text-muted">
                                    No projects found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Pagination (uses API next/previous URLs) --}}
    @php
        // Convert the API next/previous (which includes limit/offset) into local routes.
        // Example next: https://api.../projects?limit=20&offset=20
        $nextUrl = $data['next'] ?? null;
        $prevUrl = $data['previous'] ?? null;

        $nextOffset = $nextUrl ? (parse_url($nextUrl, PHP_URL_QUERY) ?? '') : null;
        $prevOffset = $prevUrl ? (parse_url($prevUrl, PHP_URL_QUERY) ?? '') : null;

        parse_str($nextOffset ?? '', $nextQuery);
        parse_str($prevOffset ?? '', $prevQuery);

        $nextOffsetVal = $nextQuery['offset'] ?? null;
        $prevOffsetVal = $prevQuery['offset'] ?? null;

        $limitVal = $nextQuery['limit'] ?? ($prevQuery['limit'] ?? request('limit', 20));
    @endphp

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted" style="font-size:13px;">
            Showing: <strong>{{ count($data['results'] ?? []) }}</strong> records
        </div>

        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary btn-sm {{ $prevUrl ? '' : 'disabled' }}"
               href="{{ $prevUrl ? url('reelly_properties') . '?limit=' . $limitVal . '&offset=' . $prevOffsetVal : '#' }}">
                ← Previous
            </a>

            <a class="btn btn-outline-primary btn-sm {{ $nextUrl ? '' : 'disabled' }}"
               href="{{ $nextUrl ? url('reelly_properties') . '?limit=' . $limitVal . '&offset=' . $nextOffsetVal : '#' }}">
                Next →
            </a>
        </div>
    </div>

</div>

@endsection
