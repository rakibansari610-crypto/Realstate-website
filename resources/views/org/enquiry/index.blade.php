@extends('layouts.admin.master')

@section('main-content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Contact Enquiry</h4>
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
                        <table id="datatable" class="table table-striped nowrap align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Message</th>
                                    <th>Detail</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
           $(function() {
                let url = "{{ route('org-contact-get') }}";
                let type = @json($types);
                $('#datatable').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: url,
                        type: "GET",
                        data: function(d) {
                            d.types = type;
                        }
                    },
                    order: [[0, "desc"]],
                    columns: [
                        { data: 'sn' },
                        { data: 'name' },
                        { data: 'email' },
                        { data: 'phone_no' },
                        { data: 'message' },
                        { data: 'detail' }, 
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            });

    </script>
@endsection

