@extends('layouts.admin.master')

@section('main-content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Properties</h4>
            <div class="page-title-right">
                @if(!auth()->user()->hasAnyRole(['channel partner']))
                <a href="{{ route('units.create') }}" class="btn btn-primary mb-3">Add Property</a>
              
                <form method="POST" action="{{ route('admin.goyzer.sync') }}">
    @csrf
    <button type="submit" class="btn btn-primary">Fetch Goyzer Data</button>
</form>
  @endif
            </div>
        </div>
    </div>
</div>
<div class="row mt-2">
   <div class="col-lg-12">
        <div class="card">
            <div class="card-body">


                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped align-middle" id="UnitsTable" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Unit For</th>
                                <th>Project</th>
                                <th>Property Name</th>
                                <th>Developer</th>
                                <th>Unit Type</th>
                                <th>Sub Type</th>
                                @hasanyrole('org admin|admin')
                                <th>Created By</th>
                                @endrole
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
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
    $('#UnitsTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('units.get') }}",
        type: 'GET'
    },
    columns: [
        { data: 'sn', name: 'units.id' },
        { data: 'unit_for', name: 'unit_for' },
        { data: 'project_name', name: 'projects.name' },
        { data: 'property_name', name: 'units.property_name' },
        { data: 'developer', name: 'developers.name' },
        { data: 'unit_type', name: 'ut.name' },
        { data: 'sub_type', name: 'st.name' },
        @hasanyrole('org admin|admin')
        { data: 'created_by', name: 'creators.name' },
        @endhasanyrole
        { 
            data: 'is_active',
            name: 'units.is_active',
            orderable: false,
            searchable: false
        },
        { 
            data: 'action', 
            name: 'action', 
            orderable: false, 
            searchable: false 
        }
    ],

    order: [[0, 'desc']],
    pageLength: 10
});

});
</script>
@endsection