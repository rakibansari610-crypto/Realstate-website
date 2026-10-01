@extends('layouts.admin.master')

@section('main-content')
  <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Seo Meta</h4>
                <div class="page-title-right">
                    <a href="{{ route('org.seo-meta.create') }}" class="btn btn-primary">Add Seo Meta</a>
                </div>
            </div>
        </div>
    </div>
<div class="row mt-2">

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                  
                  <div class="table-responsive">
                  <table class="table table-striped align-middle" id="LeadTable" width="100%">
                    <thead>
                      <tr>
                        <th>SN</th>
                        <th>Country</th>
                        <th>Page Type</th>
                        <th>Meta Title</th>
                        <th>Meta Description</th>
                        <th>Meta Keywords</th> 
                        <th class="text-end">Actions</th>
                      </tr>
                    </thead>
                     
                  </table>
                </div>
            </div>
        </div>
    </div> 
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let table = $('#LeadTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('org.seo-meta.get') }}",
            // data: function (d) {

            // }
        },
        columns: [
            { data: 'sn', name: 'sn', orderable: false } ,
            { data: 'country_id', name: 'seo_metas.country_id' },
            { data: 'page_type', name: 'page_type' },
            { data: 'meta_title', name: 'meta_title' },
            { data: 'meta_description', name: 'meta_description' },
            { data: 'meta_keywords', name: 'meta_keywords' }, 
            { data: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],  
        pageLength: 10
    }); 
});
</script>
@endsection