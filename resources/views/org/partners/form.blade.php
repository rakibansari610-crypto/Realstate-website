@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>@endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="page-title">{{ $partner->exists ? 'Edit Partner' : 'Add Partner' }}</h3>
        <a href="{{ route('org.partners.index') }}" class="btn btn-link">Back</a>
    </div>

    <form method="post" enctype="multipart/form-data" action="{{ $partner->exists ? route('org.partners.update',$partner) : route('org.partners.store') }}">
        @csrf @if($partner->exists) @method('PUT') @endif

        <div class="row g-3">

            <div class="col-md-3">
                <label class="form-label">Type *</label>
                <select name="type" class="form-select" required>
                    @foreach(['channel_partner'=>'Channel Partner','property consultant'=>'Property Consultant','freelancer'=>'Freelancer'] as $k=>$v)
                    <option value="{{ $k }}" @selected(old('type',$partner->type)===$k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Name *</label>
                <input name="name" class="form-control" required value="{{ old('name',$partner->name) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Approved</label><br>
                <input type="checkbox" name="approved" value="1" {{ old('approved',$partner->approved) ? 'checked' : '' }}>
            </div>
            <div class="col-md-2">
                <label class="form-label">Active</label><br>
                <input type="checkbox" name="active" value="1" {{ old('active',$partner->active ?? true) ? 'checked' : '' }}>
            </div>

            <div class="col-md-4">
                <label class="form-label">Email</label>
                <input name="email" class="form-control" value="{{ old('email',$partner->email) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Phone</label>
                <input name="phone" class="form-control" value="{{ old('phone',$partner->phone) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Alt Phone</label>
                <input name="alt_phone" class="form-control" value="{{ old('alt_phone',$partner->alt_phone) }}">
            </div>

            <div class="col-md-12">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address',$partner->address) }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">City</label>
                <input name="city" class="form-control" value="{{ old('city',$partner->city) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">State</label>
                <input name="state" class="form-control" value="{{ old('state',$partner->state) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Pincode</label>
                <input name="pincode" class="form-control" value="{{ old('pincode',$partner->pincode) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Parent Channel Partner (optional)</label>

                @if(auth()->user()->hasRole('channel partner'))
                {{-- Lock to logged-in channel partner --}}
                <input type="hidden" name="parent_id" value="{{ auth()->id() }}">
                <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                @else
                <select name="parent_id" class="form-select">
                    <option value="">-- None --</option>
                    @foreach($parents as $pp)
                    <option value="{{ $pp->user_id }}" @selected(old('parent_id', $partner->parent_id) == $pp->user_id)>
                        {{ $pp->name }}
                    </option>
                    @endforeach
                </select>
                @endif
            </div>
        </div>
        <div class="row g-3 mt-3">
            {{-- Documents --}}

            <div class="col-md-4">
                <label class="form-label">GST No</label>
                <input name="gst_no" class="form-control" value="{{ old('gst_no',$partner->gst_no) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">RERA No</label>
                <input name="rera_no" class="form-control" value="{{ old('rera_no',$partner->rera_no) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">PAN No</label>
                <input name="pan_no" class="form-control" value="{{ old('pan_no',$partner->pan_no) }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">GST File</label>
                <input type="file" name="gst_file" class="form-control">
                @if($partner->gst_path)<a class="small d-block mt-1" href="{{ url($partner->gst_path) }}" target="_blank">View current</a>@endif
            </div>
            <div class="col-md-4">
                <label class="form-label">RERA File</label>
                <input type="file" name="rera_file" class="form-control">
                @if($partner->rera_path)<a class="small d-block mt-1" href="{{ url($partner->rera_path) }}" target="_blank">View current</a>@endif
            </div>
            <div class="col-md-4">
                <label class="form-label">PAN File</label>
                <input type="file" name="pan_file" class="form-control">
                @if($partner->pan_path)<a class="small d-block mt-1" href="{{ url($partner->pan_path) }}" target="_blank">View current</a>@endif
            </div>
            {{-- Bank --}}
            <div class="col-md-4">
                <label class="form-label">Account Holder</label>
                <input name="bank_account_holder" class="form-control" value="{{ old('bank_account_holder',$partner->bank_account_holder) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Account No</label>
                <input name="bank_account_no" class="form-control" value="{{ old('bank_account_no',$partner->bank_account_no) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">IFSC</label>
                <input name="ifsc" class="form-control" value="{{ old('ifsc',$partner->ifsc) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Bank Name</label>
                <input name="bank_name" class="form-control" value="{{ old('bank_name',$partner->bank_name) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Branch</label>
                <input name="bank_branch" class="form-control" value="{{ old('bank_branch',$partner->bank_branch) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">UPI ID</label>
                <input name="upi_id" class="form-control" value="{{ old('upi_id',$partner->upi_id) }}">
            </div>

            {{-- Default Commission --}}
            <div class="col-md-2">
                <label class="form-label">Default Commission Type</label>
                <select name="default_commission_type" class="form-select">
                    <option value="">-- None --</option>
                    <option value="percent" @selected(old('default_commission_type',$partner->default_commission_type)=='percent')>% Percent</option>
                    <option value="flat" @selected(old('default_commission_type',$partner->default_commission_type)=='flat')>Flat</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Default Commission</label>
                <input type="number" step="0.01" name="default_commission" class="form-control" value="{{ old('default_commission',$partner->default_commission) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Relationship Manager</label>
                <select name="rm_id" class="form-select">
                    <option value="">-- Select --</option>
                    @foreach($rmUsers as $rm)
                        <option value="{{ $rm->id }}" 
                            @selected(old('rm', $partner->rm_id ?? '') == $rm->id)>
                            {{ $rm->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if(!$partner->exists)
            <div class="col-md-6">
                <label class="form-label">Password *</label>
                <div class="input-group">
                    <input name="password" type="password" class="form-control password-field" required>
                    <span class="input-group-text toggle-password" style="cursor:pointer;">
                        <i class="las la-eye"></i>
                    </span>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Confirm Password *</label>
                <div class="input-group">
                    <input name="password_confirmation" type="password" class="form-control password-field" required>
                    <span class="input-group-text toggle-password" style="cursor:pointer;">
                        <i class="las la-eye"></i>
                    </span>
                </div>
            </div>
            @endif

        </div>

        <div class="mt-4">
            <button class="btn btn-primary">{{ $partner->exists ? 'Save Changes' : 'Create Partner' }}</button>
        </div>
    </form>
</div>
@endsection
@section('scripts')
<script>
$(document).on('click', '.toggle-password', function () {

    let input = $(this).siblings('.password-field');
    let icon = $(this).find('i');

    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('la-eye').addClass('la-eye-slash');
    } else {
        input.attr('type', 'password');
        icon.removeClass('la-eye-slash').addClass('la-eye');
    }

});
</script>
@endsection