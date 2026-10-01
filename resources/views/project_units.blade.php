@extends('layouts.app')
@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h3 class="mb-1">Units</h3>
            <div class="text-muted" style="font-size:13px;">
                Project: <strong>{{ $project['name'] ?? ('#' . ($projectId ?? '')) }}</strong>
                @if(!empty($projectId))
                    • ID: <strong>{{ $projectId }}</strong>
                @endif
                @if(isset($data['count']))
                    • Total Units: <strong>{{ $data['count'] }}</strong>
                @endif
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ url('reelly/projects/' . ($projectId ?? '')) }}" class="btn btn-outline-primary btn-sm">
                ← Project Details
            </a>
            <a href="{{ url('reelly_properties') }}" class="btn btn-outline-secondary btn-sm">
                Back to Projects
            </a>
        </div>
    </div>

    {{-- Units table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:90px;">Unit ID</th>
                            <th>Title / Name</th>
                            <th class="text-end">Price</th>
                            <th>Currency</th>
                            <th class="text-end">Size</th>
                            <th>Unit</th>
                            <th>Type</th>
                            <th class="text-end">Beds</th>
                            <th class="text-end">Baths</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse(($data['results'] ?? []) as $u)
                            @php
                                // Field names can vary slightly between APIs.
                                // These safe reads cover common patterns.
                                $unitId  = $u['id'] ?? '-';
                                $title   = $u['name'] ?? ($u['title'] ?? ($u['unit_name'] ?? 'Unit'));
                                $price   = $u['price'] ?? ($u['min_price'] ?? null);
                                $curr    = $u['price_currency'] ?? ($u['currency'] ?? 'AED');
                                $size    = $u['size'] ?? ($u['area'] ?? null);
                                $areaU   = $u['area_unit'] ?? ($u['unit'] ?? 'sqft');
                                $type    = $u['type'] ?? ($u['property_type'] ?? '-');
                                $beds    = $u['bedrooms'] ?? ($u['beds'] ?? '-');
                                $baths   = $u['bathrooms'] ?? ($u['baths'] ?? '-');
                                $status  = $u['status'] ?? ($u['sale_status'] ?? '-');
                            @endphp

                            <tr>
                                <td>{{ $unitId }}</td>

                                <td>
                                    <div class="fw-semibold">{{ $title }}</div>
                                    @if(!empty($u['cover_image']['url']))
                                        <a href="{{ $u['cover_image']['url'] }}" target="_blank" style="font-size:12px;">
                                            View image
                                        </a>
                                    @endif
                                </td>

                                <td class="text-end">
                                    {{ $price !== null ? number_format((float)$price, 2) : '-' }}
                                </td>

                                <td>{{ $curr }}</td>

                                <td class="text-end">
                                    {{ $size !== null ? number_format((float)$size, 2) : '-' }}
                                </td>

                                <td>{{ $areaU }}</td>

                                <td>{{ $type }}</td>

                                <td class="text-end">{{ $beds }}</td>
                                <td class="text-end">{{ $baths }}</td>

                                <td>
                                    <span class="badge {{ $status === 'on_sale' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center p-4 text-muted">
                                    No units found for this project.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination for units (if API returns next/previous) --}}
    @php
        $nextUrl = $data['next'] ?? null;
        $prevUrl = $data['previous'] ?? null;

        $nextQ = $nextUrl ? (parse_url($nextUrl, PHP_URL_QUERY) ?? '') : null;
        $prevQ = $prevUrl ? (parse_url($prevUrl, PHP_URL_QUERY) ?? '') : null;

        parse_str($nextQ ?? '', $nextQuery);
        parse_str($prevQ ?? '', $prevQuery);

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
               href="{{ $prevUrl ? url('reelly/projects/' . ($projectId ?? '') . '/units') . '?limit=' . $limitVal . '&offset=' . $prevOffsetVal : '#' }}">
                ← Previous
            </a>

            <a class="btn btn-outline-primary btn-sm {{ $nextUrl ? '' : 'disabled' }}"
               href="{{ $nextUrl ? url('reelly/projects/' . ($projectId ?? '') . '/units') . '?limit=' . $limitVal . '&offset=' . $nextOffsetVal : '#' }}">
                Next →
            </a>
        </div>
    </div>

</div>

@endsection
