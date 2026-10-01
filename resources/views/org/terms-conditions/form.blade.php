@extends('layouts.admin.master')

@section('main-content')

@php
    $isEdit = isset($page) && $page;
@endphp

<div class="content container-fluid">

    {{-- Success --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please fix the following:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="page-title">{{ $isEdit ? 'Edit Terms & Conditions' : 'Add Terms & Conditions' }}</h3>
        <a href="{{ route('terms.conditions.index') }}" class="btn btn-link">Back</a>
    </div>

    <form method="post" action="{{ route('terms.conditions.update') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Terms & Conditions Description *</label>
            <textarea name="description" id="summernote" class="form-control" rows="12" required>
                {{ old('description', $page->description ?? '') }}
            </textarea>
        </div>

        <button class="btn btn-primary">
            {{ $isEdit ? 'Update Terms & Conditions' : 'Add Terms & Conditions' }}
        </button>
    </form>

</div>

@endsection
