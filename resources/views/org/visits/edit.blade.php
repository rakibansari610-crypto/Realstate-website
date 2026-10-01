@extends('layouts.admin.master')
@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Edit Site</h4>
                <div class="page-title-right">
                    <button type="button" onclick="history.back()" class="btn btn-light">Back</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div>


                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Fix the errors below:</strong>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="post" action="{{ route('org.sites.update', $site->id) }}">
                            @csrf @method('PUT')
                            @include('org.sites.form')
                            <div class="mt-3">
                                <button class="btn btn-primary">Save</button>
                                <a href="{{ route('org.sites.index') }}" class="btn btn-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
