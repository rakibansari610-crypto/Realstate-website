@extends('layouts.public')

@section('content')
<div class="container py-5">
  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

  <h3 class="mb-4">Register as Channel Partner / Broker / Agent</h3>
  <form method="post" enctype="multipart/form-data" action="{{ route('partner.register.store') }}">
    @csrf

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Type *</label>
        <select name="type" class="form-select" required>
          <option value="channel_partner">Channel Partner</option>
          <option value="broker">Broker</option>
          <option value="agent">Agent</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Name *</label>
        <input name="name" class="form-control" required value="{{ old('name') }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Email *</label>
        <input name="email" type="email" class="form-control" required value="{{ old('email') }}">
      </div>

      <div class="col-md-4">
        <label class="form-label">Phone *</label>
        <input name="phone" class="form-control" required value="{{ old('phone') }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Password *</label>
        <input name="password" type="password" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Confirm Password *</label>
        <input name="password_confirmation" type="password" class="form-control" required>
      </div>

      <div class="col-md-4"><label class="form-label">PAN No</label><input name="pan_no" class="form-control"></div>
      <div class="col-md-4"><label class="form-label">GST No</label><input name="gst_no" class="form-control"></div>
      <div class="col-md-4"><label class="form-label">RERA No</label><input name="rera_no" class="form-control"></div>

      <div class="col-md-4"><label class="form-label">PAN File</label><input type="file" name="pan_file" class="form-control"></div>
      <div class="col-md-4"><label class="form-label">GST File</label><input type="file" name="gst_file" class="form-control"></div>
      <div class="col-md-4"><label class="form-label">RERA File</label><input type="file" name="rera_file" class="form-control"></div>
    </div>

    <div class="mt-3"><button class="btn btn-primary">Register</button></div>
    <p class="text-muted small mt-2">Your account needs admin approval before access.</p>
  </form>
</div>
@endsection
