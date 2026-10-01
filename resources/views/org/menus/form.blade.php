@extends('layouts.admin.master')

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ isset($menu) ? 'Edit Header Menu' : 'Add Header Menu' }}</h4>
                <div class="page-title-right">
                    <a href="{{ route('org.menus.index') }}" class="btn btn-secondary">
                        <i class="las la-arrow-left"></i> Back to Menus
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-lg-8 col-md-10 offset-lg-2 offset-md-1">
            <div class="card">
                <div class="card-body">
                    <form action="{{ isset($menu) ? route('org.menus.update', encrypt($menu->id)) : route('org.menus.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Menu Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                    value="{{ old('title', $menu->title ?? '') }}" placeholder="e.g. Buy, Rent, Projects, About Us" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Parent Menu (Optional)</label>
                                <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                    <option value="">-- None (Top Level Menu) --</option>
                                    @foreach($parentMenus as $parent)
                                        <option value="{{ $parent->id }}" {{ (old('parent_id', $menu->parent_id ?? '') == $parent->id) ? 'selected' : '' }}>
                                            {{ $parent->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Select a parent menu if this should appear inside a dropdown (e.g. under 'More').</small>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">URL / Path <span class="text-danger">*</span></label>
                                <input type="text" name="url" class="form-control @error('url') is-invalid @enderror" 
                                    value="{{ old('url', $menu->url ?? '#') }}" placeholder="e.g. /buy, /about-us, or https://example.com" required>
                                <small class="text-muted">Relative URL (e.g. <code>/buy</code>) or absolute URL or <code>#</code> for dropdown parent.</small>
                                @error('url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Route Name (Optional)</label>
                                <input type="text" name="route_name" class="form-control @error('route_name') is-invalid @enderror" 
                                    value="{{ old('route_name', $menu->route_name ?? '') }}" placeholder="e.g. buy.properties, project, developers">
                                <small class="text-muted">Optional: Laravel route name if applicable.</small>
                                @error('route_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Display Order</label>
                                <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" 
                                    value="{{ old('order', $menu->order ?? 0) }}" placeholder="0">
                                <small class="text-muted">Sort order (1, 2, 3...)</small>
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Target</label>
                                <select name="target" class="form-select @error('target') is-invalid @enderror">
                                    <option value="_self" {{ (old('target', $menu->target ?? '_self') == '_self') ? 'selected' : '' }}>Same Window (_self)</option>
                                    <option value="_blank" {{ (old('target', $menu->target ?? '') == '_blank') ? 'selected' : '' }}>New Tab (_blank)</option>
                                </select>
                                @error('target')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label font-weight-bold">Status <span class="text-danger">*</span></label>
                                <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                                    <option value="1" {{ (old('is_active', $menu->is_active ?? 1) == 1) ? 'selected' : '' }}>Active (Visible)</option>
                                    <option value="0" {{ (old('is_active', $menu->is_active ?? 1) == 0) ? 'selected' : '' }}>Inactive (Hidden)</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <a href="{{ route('org.menus.index') }}" class="btn btn-light me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    {{ isset($menu) ? 'Update Menu Item' : 'Save Menu Item' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
