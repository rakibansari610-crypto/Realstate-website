@extends('layouts.admin.master')
@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Branding – {{ $org->name }}</h4>
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

                        <form method="post" action="{{ route('su.org.branding.update', $org) }}"
                            enctype="multipart/form-data">
                            @csrf @method('PUT')

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Colors & Text</div>
                                        <div class="card-body row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Sidebar Color</label>
                                                <input type="color" name="sidebar_color"
                                                    class="form-control form-control-color"
                                                    value="{{ old('primary_color', data_get($theme, 'colors.sidebar_color', '#667eea')) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Sidebar Font</label>
                                                <input type="color" name="sidebar_font_color"
                                                    class="form-control form-control-color"
                                                    value="{{ old('primary_color', data_get($theme, 'colors.sidebar_font_color', '#667eea')) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Sidebar Font Hover</label>
                                                <input type="color" name="sidebar_font_hover"
                                                    class="form-control form-control-color"
                                                    value="{{ old('primary_color', data_get($theme, 'colors.sidebar_font_hover', '#667eea')) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Primary</label>
                                                <input type="color" name="primary_color"
                                                    class="form-control form-control-color"
                                                    value="{{ old('primary_color', data_get($theme, 'colors.primary', '#667eea')) }}">
                                            </div>
                                             <div class="col-md-4">
                                                <label class="form-label">Secondary</label>
                                                <input type="color" name="primary_color"
                                                    class="form-control form-control-color"
                                                    value="{{ old('primary_color', data_get($theme, 'colors.primary', '#667eea')) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Header Text</label>
                                                <input type="color" name="header_text_color"
                                                    class="form-control form-control-color"
                                                    value="{{ old('header_text_color', data_get($theme, 'colors.header', '#ffffff')) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Login Background</label>
                                                <input type="color" name="login_bg_color"
                                                    class="form-control form-control-color"
                                                    value="{{ old('login_bg_color', data_get($theme, 'colors.login_bg', '#1e2a78')) }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Button</label>
                                                <input type="color" name="button_color"
                                                    class="form-control form-control-color"
                                                    value="{{ old('button_color', data_get($theme, 'colors.button', '#764ba2')) }}">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Login Heading</label>
                                                <input name="login_heading" class="form-control"
                                                    value="{{ old('login_heading', data_get($theme, 'text.login_h1', $org->name)) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Images</div>
                                        <div class="card-body row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Logo</label>
                                                <input type="file" name="logo" class="form-control">
                                                @if ($org->logo_path)
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/' . $org->logo_path) }}" alt="Logo"
                                                            style="height:48px;object-fit:contain;">
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Favicon (.ico or .png)</label>
                                                <input type="file" name="favicon" class="form-control">
                                                @if ($org->favicon_path)
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/' . $org->favicon_path) }}"
                                                            alt="Favicon" style="height:32px;object-fit:contain;">
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">Login Background Image</label>
                                                <input type="file" name="login_bg" class="form-control">
                                                @if ($org->login_bg_path)
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/' . $org->login_bg_path) }}"
                                                            alt="Login Background" style="height:100px;object-fit:cover;">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="mt-3">
                                <button class="btn btn-primary">Save Branding</button>
                                <a href="{{ route('su.org.index') }}" class="btn btn-light">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
