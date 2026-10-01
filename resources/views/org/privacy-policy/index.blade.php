@extends('layouts.admin.master')

@section('main-content')

<div class="content container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="page-title">Privacy Policy</h3>
        <a href="{{ route('privacy.edit') }}" class="btn btn-primary">Edit Privacy Policy</a>
    </div>

    <div class="card">
        <div class="card-body">
            {!! $page->description ?? '<p class="text-muted">No privacy policy added yet.</p>' !!}
        </div>
    </div>

</div>

@endsection
