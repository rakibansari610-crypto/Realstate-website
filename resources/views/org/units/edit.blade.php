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
<style> 
.form-select:invalid{
    border:1px solid red !important;
}
 
select:invalid + .select2-container .select2-selection--single{
    border:1px solid red !important;
}
 
select:invalid + .select2-container .select2-selection--multiple{
    border:1px solid red !important;
}


</style>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
    @php $disabled = isset($isView) && $isView ? 'disabled' : ''; @endphp
    <div class="container">
        <h3>{{ isset($unit) ? 'Edit' : 'Add' }} Property</h3>

        <form action="{{ route('units.update', $unit) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($unit))
            @endif

            <div class="row mb-2" style="background:#f7f7f7;padding:10px;">
               
                
                <div class="col-md-4 mb-3">
                    <label for="developer_id" class="form-label">Developer</label>
                    <select name="developer_id" id="developer_id"  class="form-select select2" {{ $disabled }}>
                        {{-- <option value="">-- Select Developer --</option> --}}
                        <option value="" disabled selected>-- Select Developer --</option>

                        @foreach ($developers as $developer)
                            <option value="{{ $developer->id }}"
                                {{ $unit->developer_id == $developer->id ? 'selected' : '' }}>
                                {{ $developer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="project_id" class="form-label">Project</label>
                    <select name="project_id" id="project_id"  class="form-select select2" {{ $disabled }}>
                        <option value="">-- Select Project --</option>
                        @foreach ($projects as $proj)
                            <option value="{{ $proj->id }}" {{ $unit->project_id == $proj->id ? 'selected' : '' }}>
                                {{ $proj->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-2" style="background:#f7f7f7;padding:10px;">
                <div class="col-md-3 mb-3">
                    <label for="project_id" class="form-label">Unit For *</label>
                    <select name="unit_for" id="unit_for" required class="form-select" {{ $disabled }}>
                        <option value="">-- Select --</option>
                       <option value="For Sale" {{ $unit->unit_for == 'For Sale' ? 'selected' : '' }}>For Sale</option>
                       <option value="For Rent" {{ $unit->unit_for == 'For Rent' ? 'selected' : '' }}>For Rent</option>
                       <option value="Off Plan" {{ $unit->unit_for == 'Off Plan' ? 'selected' : '' }}>Off Plan</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="project_id" class="form-label">Unit Type</label>
                    <select name="unit_type" class="form-select" id="unit_type" {{ $disabled }}>
                        <option value="">-----Select Unit Type-----</option>
                        @foreach ($unit_type as $ut)
                            <option value="{{ $ut->id }}" {{ $unit->unit_type == $ut->id ? 'selected' : '' }}>
                                {{ $ut->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Sub Type</label>
                    <select name="sub_type" id="sub_type" class="form-select" {{ $disabled }}>
                        <option value="">---Select Sub Type---</option>
                        @if (isset($unit) && $unit->sub_type)
                            <option value="{{ $unit->sub_type }}" selected>
                                {{ $sub_type->name ?? 'N/A' }}
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Plan Type</label>
                    <input type="text" name="plan_type" class="form-control"
                        value="{{ old('sub_type', $unit->plan_type ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Property Name</label>
                    <input type="text" name="property_name" class="form-control"
                        value="{{ old('property_name', $unit->property_name ?? '') }}" {{ $disabled }} required >
                </div>
                 <div class="col-md-4">
                    <label class="form-label">Meta Title </label>
                    <input name="meta_title" class="form-control"  value="{{ old('meta_title',$unit->meta_title) }}" {{ $disabled }}>
                </div>
                
                <div class="col-md-4 mb-3" >
                    <label class="form-label">Meta Keywords  </label>
                    <input name="meta_keywords" class="form-control"  value="{{ old('meta_keywords',$unit->meta_keywords) }}" {{ $disabled }} >
                </div>
                <div class="col-md-12  mb-3">
                    <label class="form-label">Meta Description </label>
                    <input name="meta_description" class="form-control"  value="{{ old('meta_description',$unit->meta_description) }}" {{ $disabled }}>
                </div>
@if($unit->exists)
                <div class="mb-3">
                    <label class="form-label">Slug *</label><br>

                    <input type="checkbox" name="autoSlug" id="autoSlug" class="form-check-input"
                    {{ $unit->is_auto_slug ? 'checked' : '' }}>
                    <label for="autoSlug">Auto Generate</label>

                    <input type="text" id="slugInput" name="project_slug" class="form-control mt-2"
                    value="{{ old('slug',$unit->slug) }}">
                </div>
                @endif
            </div>
            <div class="row mb-2" style="background:#f7f7f7;padding:10px;">
                <div class="col-md-4 mb-3">
                    <label>Street</label>
                    <input type="text" name="street" class="form-control"
                        value="{{ old('street', $unit->street ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-4 mb-3">
                    <label>City</label>
                    <input type="text" name="city_name" class="form-control"
                        value="{{ old('city_name', $unit->city_name ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Locality</label>
                    <input type="text" name="locality" class="form-control" value="{{ old('locality', $unit->locality ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Country *</label>
                    <select name="country_id" class="form-select select2" required {{ $disabled }}>
                        <option value="">-- Select Country --</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" @selected(old('country_id', $unit->country_id) == $country->id)>
                                {{ $country->country_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Latitude</label>
                    <input type="text" name="latitude" class="form-control"
                        value="{{ old('latitude', $unit->latitude ?? '') }}" {{ $disabled }}>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Longitude</label>
                    <input type="text" name="longitude" class="form-control"
                        value="{{ old('longitude', $unit->longitude ?? '') }}" {{ $disabled }}>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Google Map Link (optional)</label>
                    <input type="text" id="google_map_url" class="form-control" placeholder="https://maps.google.com/..."
                        {{ $disabled }}>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Unit Number</label>
                    <input type="text" name="unit_number" class="form-control"
                        value="{{ old('unit_number', $unit->unit_number ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Owner</label>
                    <input type="text" name="owner" class="form-control"
                        value="{{ old('owner', $unit->owner ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Listing Agent</label>
                    <input type="text" name="listing_agent" class="form-control"
                        value="{{ old('listing_agent', $unit->listing_agent ?? '') }}" {{ $disabled }}>
                </div>
                <!--<div class="col-md-4 mb-3">
                    <label>India Term</label>
                    <select name="india_term" id="" class="form-select select2">
                        <option value="">-----Select India Term-----</option>
                        <option value="flat" {{ $unit->india_term == 'flat' ? 'selected' : '' }}>Flat</option>
                        <option value="bhk" {{ $unit->india_term == 'bhk' ? 'selected' : '' }}>BHK</option>
                        <option value="resale" {{ $unit->india_term == 'resale' ? 'selected' : '' }}>Resale</option>
                        <option value="new project" {{ $unit->india_term == 'new project' ? 'selected' : '' }}>New Project</option>
                        <option value="rk" {{ $unit->india_term == 'rk' ? 'selected' : '' }}>RK</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>UAE Equivalent</label>
                    <select name="uae_term" id="" class="form-select select2">
                        <option value="">-----Select UAE Equivalent-----</option>
                        <option value="apartment" {{ $unit->uae_term == 'apartment' ? 'selected' : '' }}> Apartment</option>
                        <option value="bedroom" {{ $unit->uae_term == 'bedroom' ? 'selected' : '' }}> Bedroom</option>
                        <option value="secondary"  {{ $unit->uae_term == 'secondary' ? 'selected' : '' }}  >Secondary</option>
                        <option value="off-plan"  {{ $unit->uae_term == 'off-plan' ? 'selected' : '' }}  >Off-plan</option>
                        <option value="studio" {{ $unit->uae_term == 'studio' ? 'selected' : '' }}>Studio</option>
                    </select>
                </div>-->

            </div>
            <div class="row mb-2" style="background:#f7f7f7;padding:10px;">
                <div class="col-md-3 mb-3">
                    <label>Bedrooms</label>
                    <input type="number" name="bedrooms" class="form-control"
                        value="{{ old('bedrooms', $unit->bedrooms ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-3 mb-3">
                    <label>No of Bathrooms</label>
                    <input type="number" name="no_of_bathrooms" class="form-control"
                        value="{{ old('no_of_bathrooms', $unit->no_of_bathrooms ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Furnished</label>
                    <input type="text" name="furnished" class="form-control"
                        value="{{ old('furnished', $unit->furnished ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Unit Model</label>
                    <input type="text" name="unit_model" class="form-control"
                        value="{{ old('unit_model', $unit->unit_model ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Size</label>
                    <input type="text" name="size" class="form-control"
                        value="{{ old('size', $unit->size ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-3 mb-3">
                    <label>View</label>
                    <input type="text" name="view" class="form-control"
                        value="{{ old('view', $unit->view ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Parking Spaces</label>
                    <input type="text" name="parking_spaces" class="form-control"
                        value="{{ old('parking_spaces', $unit->parking_spaces ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Parking Number</label>
                    <input type="text" name="parking_number" class="form-control"
                        value="{{ old('parking_number', $unit->parking_number ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Parking Type</label>
                    <select name="parking_type" class="form-select" id="" {{ $disabled }}>
                        <option value="">-----Select Parking Type-----</option>
                        @foreach ($parking_type as $pt)
                            <option value="{{ $pt->id }}" {{ $unit->parking_type == $pt->id ? 'selected' : '' }}>
                                {{ $pt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Unit Fitting</label>
                    <input type="text" name="unit_fitting" class="form-control"
                        value="{{ old('unit_fitting', $unit->unit_fitting ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Plot Area</label>
                    <input type="number" step="0.01" name="plot_area" class="form-control"
                        value="{{ old('plot_area', $unit->plot_area ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Listing Date</label>
                    <input type="date" step="0.01" name="listing_date" class="form-control"
                        value="{{ old('listing_date', $unit->listing_date ?? '') }}" {{ $disabled }}>
                </div>



            </div>
            <div class="row mb-2" style="background:#f7f7f7;padding:10px;">
                <div class="col-md-3 mb-3">
                    <label>Handover Date</label>
                    <input type="date" name="handover_date" class="form-control"
                        value="{{ old('handover_date', $unit->handover_date ?? '') }}" {{ $disabled }}>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="developer_id" class="form-label">Selling Price</label>
                    <input type="text" name="selling_price" class="form-control" value="{{ $unit->selling_price }}"
                        {{ $disabled }}>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="currency_id" class="form-label">Currency</label>
                    <select name="currency_id" id="currency_id" class="form-select" {{ $disabled }}>
                        <option value="">-- Select Currecy --</option>
                        @foreach ($currency as $cr)
                            <option value="{{ $cr->id }}" {{ $unit->currency_id == $cr->id ? 'selected' : '' }}>
                                {{ $cr->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
               
                @php
                    $selectedAmenities =
                        isset($unit) && $unit->building_amenities ? explode(',', $unit->building_amenities) : [];
                @endphp
                <div class="col-md-12 mb-3">
                    <label class="form-label">Building/Property/Amenities</label>
                    <div class="select2-wrapper d-flex align-items-start gap-2">
                        <select name="building_amenities[]" class="form-select select2" multiple="multiple"
                            style="flex: 1;" {{ $disabled }}>
                            @foreach ($amenities as $am)
                                <option value="{{ $am->id }}"
                                    {{ in_array($am->id, $selectedAmenities) ? 'selected' : '' }}>{{ $am->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-dark btn-sm mt-1 clearSelect"
                            style="display:none;">Clear</button>
                    </div>
                </div>

                
               

                <div class="col-md-4 mb-3">
                    <label>Rent Price</label>
                    <input type="text" name="rent_price" class="form-control"
                        value="{{ old('rent_price', $unit->rent_price ?? '') }}" {{ $disabled }}>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Rent Period</label>
                    <select name="rent_period" class="form-select" {{ $disabled }}>
                        <option value="">-- Select --</option>
                        <option value="monthly" @selected($unit->rent_period == 'monthly')>Monthly</option>
                        <option value="yearly" @selected($unit->rent_period == 'yearly')>Yearly</option>
                    </select>
                </div>

                

                <div class="col-md-4 mb-4">
                    <label class="form-label">Featured Property</label><br>
                    <input type="checkbox" name="is_featured" value="1"
                        {{ $unit->is_featured ?? false ? 'checked' : '' }} {{ $disabled }}>
                </div>


                

                <div class="col-md-12 mb-4">
                    <label class="form-label">Property Overview</label>

                    <textarea name="property_overview" id="property_overview" class="form-control" rows="6" {{ $disabled }}>{{ old('property_overview', $unit->property_overview ?? '') }}</textarea>
                        
                    
                </div>
                <div class="col-md-4 mb-4">
                    <label class="form-label">Status </label><br>
                    <input type="checkbox" name="is_active" value="1"
                        {{ $unit->is_active == 1 ? 'checked' : '' }}{{ $disabled }}> Active
                </div>

                <div class="col-md-12 mb-3 text-left">
                    @if (!isset($isView) || !$isView)
                        <button type="submit" id="submitBtn" class="btn btn-secondary">{{ isset($unit) ? 'Update' : 'Submit' }}
                        </button>
                    @endif
                    <a href="{{ route('units.index', $project) }}" class="btn btn-warning">Cancel</a>
                </div>
                
            </div>
    </div>


    </form>

    @if(isset($unit) && $unit->exists)
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Unit Images</h4>
            </div>
            <div class="card-body">
                {{-- Upload new images --}}
                <form action="{{ route('units.images.store', [$project, $unit]) }}" method="POST" enctype="multipart/form-data" class="mb-4">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-10">
                            <label class="form-label">Upload Images</label>
                            <input type="file" name="images[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp">
                            @error('images')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            @error('images.*')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Upload</button>
                        </div>
                    </div>
                </form>

                {{-- Existing images --}}
                <div class="row">
                    @forelse($unit->unitImages as $image)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <img src="{{ $image->image_url }}" class="card-img-top" style="height:220px; object-fit:cover;">

                                <div class="card-body">
                                    <form action="{{ route('units.images.update', [$unit, $image]) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                       <!-- <div class="mb-2">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="title" class="form-control" value="{{ $image->title }}">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label">Remarks</label>
                                            <textarea name="image_remarks" class="form-control" rows="2">{{ $image->image_remarks }}</textarea>
                                        </div>-->

                                        <div class="mb-2">
                                            <label class="form-label">Sort Order</label>
                                            <input type="number" name="sort_order" class="form-control" value="{{ $image->sort_order }}">
                                        </div>

                                        <div class="form-check mb-3">
                                            <input type="hidden" name="is_marketing_image" value="0">
                                            <input class="form-check-input" type="checkbox" name="is_marketing_image" value="1"
                                                id="marketing_{{ $image->id }}"
                                                {{ $image->is_marketing_image ? 'checked' : '' }}>
                                            <label class="form-check-label" for="marketing_{{ $image->id }}">
                                                Marketing Image
                                            </label>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-sm btn-success">Update</button>
                                    </form>

                                   @if($image->source === 'manual')
    <form action="{{ route('units.images.destroy', [$unit, $image]) }}" method="POST" onsubmit="return confirm('Delete this image?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
    </form>
@endif
                                        </div>
                                </div>

                                <div class="card-footer small text-muted">
                                    Source: {{ $image->source ?? 'manual' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted mb-0">No images found for this unit.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif
    </div>
@endsection
@section('scripts')
<script>
document.querySelector("form").addEventListener("submit", function(e) {

    let valid = true;

    this.querySelectorAll("select[required]").forEach(function(el){

        if(!el.value){
            el.classList.add("is-invalid");
            valid = false;
        }else{
            el.classList.remove("is-invalid");
        }

    });

    if(!valid){
        e.preventDefault();
    }

});
</script>


    <script>
        $(document).ready(function() {


             //slug work
    
            function toggleSlug(){
                if($('#autoSlug').is(':checked')){
                    $('#slugInput').val("{{ $unit->slug }}"); // real slug show
                    $('#slugInput').prop('disabled', true);
                }else{
                    $('#slugInput').prop('disabled', false);
                }
            }

            // page load par check
            toggleSlug();

            // checkbox change par
            $('#autoSlug').change(function(){
                toggleSlug();
            });




            function loadProjects(developer_id, selected_project_id = null) {
                if (!developer_id) {
                    $('#project_id').html('<option value="">-- Select Project --</option>');
                    return;
                }

                $.ajax({
                    url: "{{ route('getProjectsByDeveloper') }}",
                    type: "GET",
                    data: {
                        developer_id: developer_id
                    },
                    success: function(response) {
                        $('#project_id').empty();
                        $('#project_id').append('<option value="">-- Select Project --</option>');

                        $.each(response, function(key, project) {
                            let selected = (selected_project_id == project.id) ? 'selected' :
                                '';
                            $('#project_id').append('<option value="' + project.id + '" ' +
                                selected + '>' + project.name + '</option>');
                        });
                    }
                });
            }

            $('#developer_id').change(function() {
                var developer_id = $(this).val();
                loadProjects(developer_id);
            });

            @if (isset($record))
                var existingDeveloperId = "{{ $record->developer_id }}";
                var existingProjectId = "{{ $record->project_id }}";
                if (existingDeveloperId) {
                    loadProjects(existingDeveloperId, existingProjectId);
                }
            @endif

            $('#unit_type').on('change', function() {
                var unit_type_id = $(this).val();
                if (!$('#sub_type').is('select')) {
                    $('input[name="sub_type"]').replaceWith(
                        '<select name="sub_type" class="form-select" id="sub_type"><option value="">-----Select Sub Type-----</option></select>'
                    );
                }

                if (unit_type_id) {
                    $('#sub_type').html('<option>Loading...</option>');
                    $.ajax({
                        url: "{{ route('get-sub-types') }}",
                        type: "GET",
                        data: {
                            unit_type_id: unit_type_id
                        },
                        success: function(res) {
                            $('#sub_type').empty();
                            $('#sub_type').append(
                                '<option value="">-----Select Sub Type-----</option>');
                            $.each(res, function(key, value) {
                                $('#sub_type').append('<option value="' + value.id +
                                    '">' + value.name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#sub_type').html('<option value="">-----Select Sub Type-----</option>');
                }
            });

            @if (isset($unit) && $unit->unit_type)
                $.ajax({
                    url: "{{ route('get-sub-types') }}",
                    type: "GET",
                    data: {
                        unit_type_id: "{{ $unit->unit_type }}"
                    },
                    success: function(res) {
                        // console.log(res);
                        $('#sub_type').empty();
                        $('#sub_type').append('<option value="">-----Select Sub Type-----</option>');
                        $.each(res, function(key, value) {
                            let selected = "{{ $unit->sub_type }}" == value.id ? 'selected' :
                                '';
                            $('#sub_type').append('<option value="' + value.id + '" ' +
                                selected + '>' + value.name + '</option>');
                        });
                    }
                });
            @endif






        });
    </script>
@endsection
