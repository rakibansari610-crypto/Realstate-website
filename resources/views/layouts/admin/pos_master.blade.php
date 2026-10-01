@php
    $uiSetting = getUISetting();
    $selectedCompany = getSelectedCompany();
@endphp
@include('layouts.admin.include.pos_head')


<div class="vertical-overlay"></div>

<div class="main-content" style="margin:0px;">
    <div class="page-content" style="padding-top:10px;">
        @yield('main-content')
    </div>

   
</div>
<div class="modal fade" id="docEmailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Email Document</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form id="docEmailForm" method="post" action="{{ route('admin.docs.email.send') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-body" id="docEmailModalBody">
        {{-- filled via AJAX OR you can inline the form below instead --}}
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">Send Email</button>
      </div>
    </form>
  </div></div>
</div>
@include('layouts.admin.include.footer')