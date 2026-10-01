@extends('layouts.admin.master')

@section('main-content')
<div class="content container-fluid">
  @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

  <h3 class="page-title">Add Channel Partner (Team)</h3>
  <form method="post" enctype="multipart/form-data" action="{{ route('org.partners.team.store') }}">
    @csrf

    <input type="hidden" name="type" value="channel_partner">

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Name *</label>
        <input name="name" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Email</label>
        <input name="email" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Phone</label>
        <input name="phone" class="form-control">
      </div>

      {{-- Optional docs & bank similar to admin form --}}
      <div class="col-md-4"><label class="form-label">PAN No</label><input name="pan_no" class="form-control"></div>
      <div class="col-md-4"><label class="form-label">GST No</label><input name="gst_no" class="form-control"></div>
      <div class="col-md-4"><label class="form-label">RERA No</label><input name="rera_no" class="form-control"></div>

      <div class="col-md-4"><label class="form-label">PAN File</label><input type="file" name="pan_file" class="form-control"></div>
      <div class="col-md-4"><label class="form-label">GST File</label><input type="file" name="gst_file" class="form-control"></div>
      <div class="col-md-4"><label class="form-label">RERA File</label><input type="file" name="rera_file" class="form-control"></div>
    </div>

    <div class="mt-3"><button class="btn btn-primary">Create</button></div>
  </form>
</div>
@endsection
