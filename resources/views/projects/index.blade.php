@extends('layouts.app') {{-- adjust to your actual layout name --}}

@section('content')
<div class="container py-5">

    <h1 class="mb-4">Our Projects</h1>

    @if ($error)
        <div class="alert alert-warning">{{ $error }}</div>
    @endif

    {{-- Optional search box --}}
    <form method="GET" class="mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects...">
        <button type="submit">Search</button>
    </form>

    <div class="row">
        @forelse ($projects as $project)
            <div class="col-md-4 mb-4">
                <div class="card h-100">

                    @if (!empty($project['imageUrls'][0]))
                        <img src="{{ $project['imageUrls'][0] }}"
                             class="card-img-top"
                             alt="{{ $project['name'] ?? 'Project' }}"
                             style="height:200px; object-fit:cover;">
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $project['name'] ?? 'Untitled Project' }}</h5>

                        @if (!empty($project['currentStatus']))
                            <span class="badge bg-success mb-2">{{ $project['currentStatus'] }}</span>
                        @endif

                        @if (!empty($project['description']))
                            <p class="card-text text-muted">
                                {{ \Illuminate\Support\Str::limit($project['description'], 100) }}
                            </p>
                        @endif

                        @if (!empty($project['address']))
                            <p class="small text-muted mb-1">
                                📍 {{ is_array($project['address']) ? ($project['address']['locality'] ?? $project['address']['city'] ?? '') : $project['address'] }}
                            </p>
                        @endif

                        {{-- Uncomment when you have a project detail route --}}
                        {{-- <a href="{{ route('projects.show', $project['id']) }}" class="btn btn-primary btn-sm">View Details</a> --}}
                    </div>
                </div>
            </div>
        @empty
            <p>No projects found.</p>
        @endforelse
    </div>

    {{-- Simple pagination --}}
    <div class="mt-4">
        <p>Total Projects: {{ $totalCount }}</p>
        @if (request('page', 1) > 1)
            <a href="?page={{ request('page', 1) - 1 }}">&laquo; Previous</a>
        @endif
        <a href="?page={{ request('page', 1) + 1 }}">Next &raquo;</a>
    </div>

</div>
@endsection