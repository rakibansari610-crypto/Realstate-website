@extends('layouts.admin.master')

@section('main-content')
<style>
    .select2-container .select2-selection--single {
    height: 38px;          /* select ke height ki css h yeh*/
    padding: 5px 12px;
}

/* Text vertically center karne ke liye */
.select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 32px;     /* height - padding ke hisaab se */
}

/* Dropdown arrow center */
.select2-container .select2-selection--single .select2-selection__arrow {
    height: 40px;
}
</style>
<div class="container">
    <h3>{{ isset($unit) ? 'Edit' : 'Add' }} Property</h3>

     @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
        <div class="fw-semibold mb-1">Please fix the following:</div>
        <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
        </div>
    @endif
    <form action="{{route('units.store') }}" method="POST">
    {{-- <form action="{{route('projects.units.store', $project) }}" method="POST"> --}}
        @csrf
        @if(isset($unit))
        @method('PUT')
        @endif

        <div class="row mb-2"  style="background:#f7f7f7;padding:10px;">
            
           
            <div class="col-md-4 mb-3">
                <label for="developer_id" class="form-label">Developer</label>
                <select name="developer_id" id="developer_id" class="form-select select2">
                    <option value="">-- Select Developer --</option>
                    @foreach ($developers as $developer)
                    <option value="{{ $developer->id }}">
                        {{ $developer->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="project_id" class="form-label">Project</label>
                <select name="project_id" id="project_id" class="form-select select2" >
                    <option value="">-- Select Project --</option>
                    a
                </select>
            </div>
           
        </div>
<div class="row mb-2"  style="background:#f7f7f7;padding:10px;">
         <div class="col-md-3 mb-3">
                <label for="project_id" class="form-label">Unit For *</label>
                <select name="unit_for" id="unit_for"  class="form-select" >
                    <option value="">-- Select --</option>
                    <option vale="buy">For Sale</option>
                    <option vale="rent">For Rent</option>
                    <option vale="offplan">Off Plan</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="project_id" class="form-label">Unit Type</label>
                <select name="unit_type" class="form-select" id="unit_type">
                    <option value="">-----Select Unit Type-----</option>
                    @foreach ($unit_type as $ut)
                    <option value="{{$ut->id}}"   >{{$ut->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label>Sub Type</label>
                <select name="sub_type" id="sub_type" class="form-select">
                    <option value="">---Select Sub Type---</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label>Plan Type</label>
                <input type="text" name="plan_type" class="form-control" value="{{ old('sub_type', $unit->plan_type ?? '') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Property Name</label>
                <input type="text" name="property_name" class="form-control" value="{{ old('property_name', $unit->property_name ?? '') }}" >
            </div>
           
             <div class="col-md-4">
            <label class="form-label">Meta Title </label>
            <input name="meta_title" class="form-control"  value="{{ old('meta_title') }}" >
          </div>
          <div class="col-md-4 mb-4">
            <label class="form-label">Meta Keywords  </label>
            <input name="meta_keywords" class="form-control"  value="{{ old('meta_keywords') }}"   >
          </div>
          <div class="col-md-12">
            <label class="form-label">Meta Description </label>
            <input name="meta_description" class="form-control"  value="{{ old('meta_description') }}">
          </div>
          
</div>
        <div class="row mb-2"  style="background:#f7f7f7;padding:10px;">
           
            <div class="col-md-4 mb-3">
                <label>Street</label>
                <input type="text" name="street" class="form-control" value="{{ old('street', $unit->street ?? '') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>City</label>
                <input type="text" name="city_name" class="form-control" value="{{ old('city_name') }}" >
            </div>
            <div class="col-md-4 mb-3">
                <label>Locality</label>
                <input type="text" name="locality" class="form-control" value="{{ old('locality') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Country *</label>
                <select name="country_id" class="form-select select2" >
                    <option value="">-- Select Country --</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" >
                            {{ $country->country_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Unit Number</label>
                <input type="text" name="unit_number" class="form-control" value="{{ old('unit_number', $unit->unit_number ?? '') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label>Owner</label>
                <input type="text" name="owner" class="form-control" value="">
            </div>
            <div class="col-md-3 mb-3">
                <label>Listing Agent</label>
                <input type="text" name="listing_agent" class="form-control" value="">
            </div>
    <!--<div class="col-md-3 mb-3">
        <label>India Term</label>
        <select name="india_term" id="" class="form-select select2">
            <option value="">-----Select India Term-----</option>
            <option value="flat">Flat</option>
            <option value="bhk">BHK</option>
            <option value="resale">Resale</option>
            <option value="new project">New Project</option>
            <option value="rk">RK</option>
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label>UAE Equivalent</label>
        <select name="uae_term" id="" class="form-select select2">
            <option value="">-----Select UAE Equivalent-----</option>
            <option value="apartment">Apartment</option>
            <option value="bedroom">Bedroom</option>
            <option value="secondary">Secondary</option>
            <option value="off-plan">Off-plan</option>
            <option value="studio">Studio</option>
        </select>
    </div>-->
    </div>
    <div class="row mb-2"  style="background:#f7f7f7;padding:10px;">


        <div class="col-md-3 mb-3">
            <label>Bedrooms</label>
            <input type="number" name="bedrooms" class="form-control" value="{{ old('bedrooms', $unit->bedrooms ?? '') }}">
        </div>
        <div class="col-md-3 mb-3">
            <label>No Of Bathrooms</label>
            <input type="number" name="no_of_bathrooms" class="form-control" value="{{ old('no_of_bathrooms', $unit->no_of_bathrooms ?? '') }}">
        </div>
        <div class="col-md-3 mb-3">
            <label>Furnished</label>
            <input type="text" name="furnished" class="form-control" value="{{ old('furnished', $unit->furnished ?? '') }}">
        </div>
        <div class="col-md-3 mb-3">
            <label>Unit Model</label>
            <input type="text" name="unit_model" class="form-control" value="{{ old('unit_model', $unit->unit_model ?? '') }}">
        </div>
        <div class="col-md-3 mb-3">
            <label>Size</label>
            <input type="text" name="size" class="form-control" value="{{ old('size', $unit->size ?? '') }}">
        </div>
        <div class="col-md-3 mb-3">
            <label>View</label>
            <input type="text" name="view" class="form-control" value="{{ old('view', $unit->view ?? '') }}">
        </div>
        <div class="col-md-3 mb-3">
            <label>Parking Spaces</label>
            <input type="text" name="parking_spaces" class="form-control" value="{{ old('parking_spaces', $unit->parking_spaces ?? '') }}">
        </div>
        <div class="col-md-3 mb-3">
            <label>Parking Number</label>
            <input type="text" name="parking_number" class="form-control" value="{{ old('parking_number', $unit->parking_number ?? '') }}">
        </div>
        <div class="col-md-3 mb-3">
            <label>Parking Type</label> 
            <select name="parking_type" class="form-select" id="">
                <option value="">-----Select Parking Type-----</option>
                @foreach ($parking_type as $pt)
                <option value="{{$pt->id}}"     >{{$pt->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label>Unit Fitting</label>
            <input type="text" name="unit_fitting" class="form-control" value="{{ old('unit_fitting', $unit->unit_fitting ?? '') }}">
        </div>
        <div class="col-md-3 mb-3">
            <label>Plot Area</label>
            <input type="number" step="0.01" name="plot_area" class="form-control" value="{{ old('plot_area', $unit->plot_area ?? '') }}">
        </div>
        <div class="col-md-3 mb-3">
            <label>Listing Date</label>
            <input type="date" step="0.01" name="listing_date" class="form-control" value="{{ old('listing_date', $unit->listing_date ?? '') }}">
        </div>
        
        
        
        
    </div>
    <div class="row mb-2" style="background:#f7f7f7;padding:10px;">
        <div class="col-md-3 mb-3">
            <label>Handover Date</label>
            <input type="date" name="handover_date" class="form-control" value="{{ old('handover_date', $unit->handover_date ?? '') }}">
        </div>
        <div class="col-md-4 mb-3">
            <label for="developer_id" class="form-label">Selling Price</label>
            <input type="text" name="selling_price" class="form-control">
        </div>

        <div class="col-md-4 mb-3">
            <label for="currency_id" class="form-label">Currency</label>
            <div class="d-flex align-items-start gap-2">
                <select name="currency_id" id="currency_id" class="form-select " >
                    <option value="">-- Select Currency --</option>
                    @foreach ($currency as $cr)
                        <option value="{{ $cr->id }}">{{ $cr->name }}</option>
                    @endforeach
                </select> 
            </div>
        </div>
       
        <div class="col-md-6 mb-3">
            <label class="form-label">Building/Property/Amenities</label>
            <div class="select2-wrapper d-flex align-items-start gap-2">
                <select name="building_amenities[]" class="form-select select2" multiple="multiple" style="flex: 1;">
                    @foreach ($amenities as $am)
                        <option value="{{ $am->id }}">{{ $am->name }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-dark btn-sm mt-1 clearSelect" style="display:none;">Clear</button>
            </div>
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label">Status</label><br>
            <input type="checkbox" name="status" value="1"> Active
        </div>
        <div class="col-md-12 mb-3 text-left">
        <button type="submit" class="btn btn-secondary">{{ isset($unit) ? 'Update' : 'Submit' }} </button>
        <a href="{{ route('units.index') }}" class="btn btn-warning">Cancel</a>
        {{-- <a href="{{ route('projects.units.index', $project) }}" class="btn btn-warning">Cancel</a> --}}
        </div>
    </div>
 <div class="row mb-2"  style="padding:10px;">
 

</div>
</form>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function () {
    $('#developer_id').change(function () {
        var developer_id = $(this).val(); 
        if (developer_id === "") {
            $('#project_id').html('<option value="">-- Select Project --</option>');
            return;
        }
        $.ajax({
            url: "{{ route('getProjectsByDeveloper') }}", // route name use karo
            type: "GET"
            , data: {
                developer_id: developer_id
            }
            , success: function(response) {
                $('#project_id').empty();
                $('#project_id').append('<option value="">-- Select Project --</option>');

                $.each(response, function(key, project) {
                    $('#project_id').append('<option value="' + project.id + '">' + project.name + '</option>');
                });
            }
            , error: function() {
                alert('Something went wrong while fetching projects!');
            }
        });
    });


    $('#unit_type').on('change', function() {
            var unit_type_id = $(this).val();
            if(!$('#sub_type').is('select')){
                $('input[name="sub_type"]').replaceWith('<select name="sub_type" class="form-select" id="sub_type"><option value="">-----Select Sub Type-----</option></select>');
            }

            if (unit_type_id) {
                $('#sub_type').html('<option>Loading...</option>');
                $.ajax({
                    url: "{{ route('get-sub-types') }}",
                    type: "GET",
                    data: { unit_type_id: unit_type_id },
                    success: function(res) {
                        $('#sub_type').empty();
                        $('#sub_type').append('<option value="">-----Select Sub Type-----</option>');
                        $.each(res, function(key, value) {
                            $('#sub_type').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            } else {
                $('#sub_type').html('<option value="">-----Select Sub Type-----</option>');
            }
        });
 
});




</script>


@endsection
