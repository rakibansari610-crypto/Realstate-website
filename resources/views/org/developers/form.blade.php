@extends('layouts.admin.master')

@section('main-content')
@php $disabled = isset($isView) && $isView ? 'disabled' : ''; @endphp

<div class="content container-fluid">
   @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>@endif
  <h3 class="page-title">{{ $developer->exists ? 'Edit Developer' : 'Add Developer' }}</h3>

  <form method="post" enctype="multipart/form-data"
        action="{{ $developer->exists ? route('org.developers.update',$developer) : route('org.developers.store') }}">
    @csrf
    @if($developer->exists) @method('PUT') @endif

    <div class="row">
      <div class="col-md-3 mb-3">
        <label class="form-label">Name *</label>
        <input name="name" class="form-control" required value="{{ old('name',$developer->name) }}" {{ $disabled }}>
        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Website</label>
        <input name="website" class="form-control" value="{{ old('website',$developer->website) }}" {{ $disabled }}>
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" value="{{ old('email',$developer->email) }}" {{ $disabled }}>
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Phone</label>
        <input name="phone" class="form-control" value="{{ old('phone',$developer->phone) }}" {{ $disabled }}>
      </div>
      <div class="col-12 mb-3">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" {{ $disabled }} rows="2">{{ old('address',$developer->address) }}</textarea>
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">City</label>
        <input name="city" class="form-control" value="{{ old('city',$developer->city) }}" {{ $disabled }}>
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">State</label>
        <input name="state" class="form-control" value="{{ old('state',$developer->state) }}" {{ $disabled }}>
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Zip</label>
        <input name="zip" class="form-control" value="{{ old('zip',$developer->zip) }}" {{ $disabled }}>
      </div>
      {{-- <div class="col-md-3 mb-3">
        <label class="form-label">Country</label>
        <input name="country" class="form-control" value="{{ old('country',$developer->country) }}" {{ $disabled }}>
      </div> --}}
      {{-- <div class="col-md-6 mb-3">
          <label class="form-label">Countries</label>
          <select name="countries[]" class="form-control" multiple {{ $disabled }}>
        @foreach($countries as $country)
            <option value="{{ $country->id }}"
                {{ in_array(
                    $country->id,
                    old('countries', $developer->countries->pluck('id')->toArray() ?? [])
                ) ? 'selected' : '' }}>
                {{ $country->country_name }}
            </option>
        @endforeach
          </select>
      </div> --}}
      <div class="col-md-6 mb-3">
        <label class="form-label">Countries</label>
      <select name="countries" class="form-control" {{ $disabled }}>
        @foreach($countries as $country)
            <option value="{{ $country->id }}"
                {{ old('countries', $developer->country ?? '') == $country->id ? 'selected' : '' }}>
                {{ $country->country_name }}
            </option>
        @endforeach
    </select>

</div>


      <div class="col-md-3 mb-3">
        <label class="form-label">Logo (jpg/png/webp, max 2MB)</label>
        <input type="file" name="logo" class="form-control" {{ $disabled }}>
        @if($developer->logo_path)
          <img src="{{ Storage::disk('public')->url($developer->logo_path) }}" style="height:60px" class="mt-2">
        @endif
        @error('logo')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Active</label><br>
        <input type="checkbox" name="active" value="1" {{ old('active',$developer->active) ? 'checked' : '' }} {{ $disabled }}>
      </div>
    </div>
    @if(!isset($isView) || !$isView)
    <button class="btn btn-primary">{{ $developer->exists ? 'Save Changes' : 'Create Developer' }}</button>
    @endif
    <a href="{{ route('org.developers.index') }}" class="btn btn-link">Cancel</a>
  </form>
</div>
@endsection
