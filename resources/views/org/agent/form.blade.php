@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">

    @php
        $isEdit = isset($agent) && isset($agent->id);
    @endphp

    {{-- Success --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="page-title">
            {{ $isEdit ? 'Edit Agent' : 'Add Agent' }}
        </h3>
        <a href="{{ route('org.agent-index') }}" class="btn btn-secondary">Back</a>
    </div>

   <form method="POST"
      enctype="multipart/form-data"
      action="{{ route('org.agent-store', $isEdit ? $agent->id : null) }}">
        @csrf

        <div class="row g-3">

            {{-- Name --}}
            <div class="col-md-4">
                <label class="form-label">Name *</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name', $isEdit ? $agent->name : '') }}"
                       required>
            </div>

            {{-- Email --}}
            <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email"
                       name="email"
                       class="form-control"
                       value="{{ old('email', $isEdit ? $agent->email : '') }}">
            </div>

            {{-- Phone --}}
            <div class="col-md-4">
                <label class="form-label">Phone</label>
                <input type="text"
                       name="phone"
                       class="form-control"
                       value="{{ old('phone', $isEdit ? $agent->phone : '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Designation</label>
                <input type="text"
                       name="designation"
                       class="form-control"
                       value="{{ old('designation', $isEdit ? $agent->designation : '') }}">
            </div>
            {{-- Profile Image --}}
            <div class="col-md-6">
                <label class="form-label">Profile  (jpg/png/webp, max 5MB)</label>
                <input type="file" name="profile_image" class="form-control"  accept="image/jpeg,image/png,image/webp">

                @if($isEdit && !empty($agent->profile_image))
                    <img src="{{ asset($agent->profile_image) }}"
                         width="120"
                         class="mt-2 rounded shadow">
                @endif
            </div>

            {{-- Address --}}
            <div class="col-md-12">
                <label class="form-label">Address</label>
                <textarea name="address"
                          class="form-control"
                          rows="2">{{ old('address', $isEdit ? $agent->address : '') }}</textarea>
            </div>

            {{----country---}}
            <div class="col-md-4">
                <label class="form-label">Country</label>
                <select name="country" class="form-select">
                    <option value="">Select Country</option>
                    @foreach($countries as $country)   
                        <option value="{{$country->id}}"  {{ old('country', $isEdit ? $agent->country : '') == $country->id ? 'selected' : '' }}>{{$country->country_name}}</option>
                    @endforeach
                </select>
            </div>

            {{-- City --}}
            <div class="col-md-4">
                <label class="form-label">City</label>
                <input type="text"
                       name="city"
                       class="form-control"
                       value="{{ old('city', $isEdit ? $agent->city : '') }}">
            </div>

            {{-- State --}}
            <div class="col-md-4">
                <label class="form-label">State</label>
                <input type="text"
                       name="state"
                       class="form-control"
                       value="{{ old('state', $isEdit ? $agent->state : '') }}">
            </div>

            {{-- Pincode --}}
            <div class="col-md-4">
                <label class="form-label">Pincode</label>
                <input type="text"
                       name="pincode"
                       class="form-control"
                       value="{{ old('pincode', $isEdit ? $agent->pincode : '') }}">
            </div>

        </div>

        {{-- Social Links --}}
        <div class="row g-3 mt-4">
            <div class="col-12">
                <h5>Social Links</h5>
            </div>

            <div class="col-md-4">
                <label class="form-label">Facebook URL</label>
                <input type="text"
                       name="facebook"
                       class="form-control"
                       value="{{ old('facebook', $isEdit ? $agent->facebook : '') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">LinkedIn URL</label>
                <input type="text"
                       name="linkedin"
                       class="form-control"
                       value="{{ old('linkedin', $isEdit ? $agent->linkedin : '') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Twitter URL</label>
                <input type="text"
                       name="twitter"
                       class="form-control"
                       value="{{ old('twitter', $isEdit ? $agent->twitter : '') }}">
            </div>
        </div>

        {{-- Active --}}
        <div class="mt-4">
            <label class="form-label">Active</label><br>
            <input type="checkbox"
                   name="active"
                   value="1"
                   {{ old('active', $isEdit ? $agent->is_active : true) ? 'checked' : '' }}>
        </div>

        {{-- Submit --}}
        <div class="mt-4">
            <button class="btn btn-primary px-4">
                {{ $isEdit ? 'Update Agent' : 'Create Agent' }}
            </button>
        </div>

    </form>

</div>
@endsection
