@extends('layouts.admin.master')

@section('main-content')
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            /* select ke height ki css h yeh*/
            padding: 5px 12px;
        }

        /* Text vertically center karne ke liye */
        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 32px;
            /* height - padding ke hisaab se */
        }

        /* Dropdown arrow center */
        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .select-loader {
            font-style: italic;
            color: #999;
        }
    </style>

    @php $disabled = isset($isView) && $isView ? 'disabled' : ''; @endphp
    <div class="content container-fluid">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">Please fix the following:</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="page-title">{{ $project->exists ? 'Edit Project' : 'Add Project' }}</h3>
            @if ($project->exists)
                <div>
                    <a href="{{ route('org.projects.units.index', $project) }}" class="btn btn-outline-primary btn-sm">Manage
                        Units</a>
                    <a href="{{ route('org.projects.materials.index', $project) }}"
                        class="btn btn-outline-info btn-sm">Materials</a>
                </div>
            @endif
        </div>

        <ul class="nav nav-tabs" id="projectTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-tab="details" id="details-tab" data-bs-toggle="tab"
                    data-bs-target="#details" type="button" role="tab">
                    Project Details
                </button>
            </li>
            @if ($project->exists)
                <li class="nav-item">
                    <button class="nav-link" data-tab="amenities" id="amenities-tab" data-bs-toggle="tab"
                        data-bs-target="#amenities" type="button" role="tab">
                        Amenities
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-tab="floorPlan" id="floorPlan-tab" data-bs-toggle="tab"
                        data-bs-target="#floorPlan" type="button" role="tab">
                        Floor Plan
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-tab="projectMedia" id="projectMedia-tab" data-bs-toggle="tab"
                        data-bs-target="#projectMedia" type="button" role="tab">
                        Project Media
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-tab="paymentPlan" id="paymentPlan-tab" data-bs-toggle="tab"
                        data-bs-target="#paymentPlan" type="button" role="tab">
                        Payment Plan
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-tab="NearByPlan" id="NearByPlan-tab" data-bs-toggle="tab"
                        data-bs-target="#NearByPlan" type="button" role="tab">
                        Near By Plan
                    </button>
                </li>
            @endif
        </ul>

        <div class="tab-content mt-3" id="projectTabsContent">
            <div class="tab-pane fade show active" id="details" role="tabpanel">
                <form method="post" enctype="multipart/form-data"
                    action="{{ $project->exists ? route('org.projects.update', $project) : route('org.projects.store') }}">
                    @csrf
                    @if ($project->exists)
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Meta Title </label>
                            <input name="meta_title" class="form-control"
                                value="{{ old('meta_title', $project->meta_title) }}" {{ $disabled }}>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Meta Description </label>
                            <input name="meta_description" class="form-control"
                                value="{{ old('meta_description', $project->meta_description) }}" {{ $disabled }}>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Meta Keywords </label>
                            <input name="meta_keywords" class="form-control"
                                value="{{ old('meta_keywords', $project->meta_keywords) }}" {{ $disabled }}>
                        </div>
                        @if ($project->exists)
                            <div class="mb-2">
                                <label class="form-label">Slug *</label><br>

                                <input type="checkbox" name="autoSlug" id="autoSlug" class="form-check-input"
                                    {{ $project->is_auto_slug ? 'checked' : '' }}>
                                <label for="autoSlug">Auto Generate</label>

                                <input type="text" id="slugInput" name="project_slug" class="form-control mt-2"
                                    value="{{ old('slug', $project->slug) }}">
                            </div>
                        @endif
                        <div class="col-md-4">
                            <label class="form-label">Name *</label>
                            <input name="name" class="form-control" required
                                value="{{ old('name', $project->name) }}" {{ $disabled }}>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Country *</label>
                            <select name="country_id" id="country_id" class="form-select" required {{ $disabled }}>
                                <option value="">-- Select Country --</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @selected(old('country_id', $project->country_id) == $country->id)>
                                        {{ $country->country_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Developer *</label>
                            <select name="developer_id" id="developer_id" class="form-select select2" required
                                {{ $disabled }}
                                data-selected="{{ old('developer_id', $project->developer_id ?? '') }}">
                                <option value="">-- Select Developer --</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">RERA No.</label>
                            <input name="rera_no" class="form-control" value="{{ old('rera_no', $project->rera_no) }}"
                                {{ $disabled }}>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">City</label>
                            <input name="city" class="form-control" value="{{ old('city', $project->city) }}"
                                {{ $disabled }}>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Location</label>
                            <input name="location" class="form-control"
                                value="{{ old('location', $project->location) }}" {{ $disabled }}>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Locality </label>
                            <input name="locality" class="form-control"
                                value="{{ old('locality', $project->locality) }}" {{ $disabled }}>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Pincode</label>
                            <input name="pincode" class="form-control" value="{{ old('pincode', $project->pincode) }}"
                                {{ $disabled }}>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status *</label>
                            <select name="status" class="form-select" required {{ $disabled }}>
                                @foreach (['planning', 'launched', 'under_construction', 'ready_to_move', 'completed'] as $s)
                                    <option value="{{ $s }}" @selected(old('status', $project->status ?? 'planning') == $s)>
                                        {{ ucwords(str_replace('_', ' ', $s)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sale Status *</label>
                            <select name="sale_status" class="form-select" required {{ $disabled }}>
                                @foreach (['out_of_stock', 'on_sale', 'presale', 'start_of_sales', 'announced'] as $s)
                                    <option value="{{ $s }}" @selected(old('sale_status', $project->sale_status ?? 'selected') == $s)>
                                        {{ ucwords(str_replace('_', ' ', $s)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Launch Date</label>
                            <input type="date" name="launch_date" class="form-control"
                                value="{{ old('launch_date', $project->launch_date) }}" {{ $disabled }}>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Completion Date</label>
                            <input type="date" name="completion_date" class="form-control"
                                value="{{ old('completion_date', $project->completion_date) }}" {{ $disabled }}>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Min Price</label>
                            <input type="number" step="0.01" readonly name="min_price" class="form-control"
                                value="{{ old('min_price',  $minPrice ?? $project->min_price) }}"  {{ isset($project->id) ? 'readonly' : '' }} {{ $disabled }}>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Max Price</label>
                            <input type="number" step="0.01" readonly name="max_price" class="form-control" value="{{ old('max_price', $maxPrice ?? $project->max_price) }}" {{ isset($project->id) ? 'readonly' : '' }} {{ $disabled }}>
                        </div>


                        <div class="col-md-3">
                            <label class="form-label">Latitude</label>
                            <input type="number" step="0.0000001" name="lat" class="form-control"
                                value="{{ old('lat', $project->lat) }}" {{ $disabled }}>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Longitude</label>
                            <input type="number" step="0.0000001" name="lng" class="form-control"
                                value="{{ old('lng', $project->lng) }}" {{ $disabled }}>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Cover Image (jpg/png/webp, max 4MB)</label>
                            <input type="file" name="cover" class="form-control" {{ $disabled }}>
                            @if ($project->cover_image_path)
                                <div class="mt-2">
                                    <img src="{{ Storage::disk('public')->url($project->cover_image_path) }}"
                                        style="height:80px;border-radius:4px;">
                                </div>
                            @endif
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Handover</label>
                            <input type="text" name="handover" class="form-control"
                                value="{{ old('handover', $project->handover) }}" {{ $disabled }}>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Payment Plan</label>
                            <input type="number" name="payment_plan" class="form-control"
                                value="{{ old('payment_plan', $project->payment_plan) }}" {{ $disabled }}>
                        </div>
                        {{-- <div class="col-md-3">
                            <label class="form-label">Project Type </label>
                            <select name="project_type" class="form-select" id="project_type" {{ $disabled }}>
                                <option value="">----Select ---</option>
                                <option value="apartment" {{ $project->project_type == 'apartment' ? 'selected' : '' }}>
                                    Apartment</option>
                                <option value="villa" {{ $project->project_type == 'villa' ? 'selected' : '' }}>Villa
                                </option>
                                <option value="townhouse" {{ $project->project_type == 'townhouse' ? 'selected' : '' }}>
                                    Townhouse</option>
                            </select>
                        </div> --}}
                       <div class="col-md-3">
                            <label class="form-label">Category Type </label>
                            <select name="category_type" class="form-select" id="project_type" {{ $disabled }}>
                                <option value="">----Select ---</option>
                                <option value="Commercial" {{ $project->category_type == 'Commercial' ? 'selected' : '' }}>
                                    Commercial
                                </option>
                                <option value="Residential" {{ $project->category_type == 'Residential' ? 'selected' : '' }}>
                                    Residential
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Sub Category</label>
                            <select name="sub_category" class="form-select" id="sub_category">
                                <option value="">----Select ---</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Net Area or Size</label>
                            <input type="text" name="area_size" class="form-control"
                                value="{{ old('area_size', $project->area_size) }}" {{ $disabled }}>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Post Handover Payment Plan</label>
                            <input type="number" name="handover_payment_plan" class="form-control"
                                value="{{ old('handover_payment_plan', $project->handover_payment_plan) }}"
                                {{ $disabled }}>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Building Description</label>
                            <textarea name="building_description" id="summernote" {{ $disabled }} class="form-control" rows="8">{{ old('building_description', $project->building_description) }}</textarea>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Active</label><br>
                            <input type="checkbox" name="active" value="1"
                                {{ old('active', $project->active ?? true) ? 'checked' : '' }} {{ $disabled }}>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Proper Draft</label><br>
                            <input type="checkbox" name="draft" value="1"
                                {{ old('draft', $project->draft ?? 0) == 1 ? 'checked' : '' }} {{ $disabled }}>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Featured {{$project->featured}}</label><br>
                            <input type="checkbox" 
               name="featured" 
               id="featured"
               class="form-check-input"
               value="1"
              {{ old('featured', $project->featured ?? 0) == 1 ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="mt-4">
                        @if (!isset($isView) || !$isView)
                            <button
                                class="btn btn-primary">{{ $project->exists ? 'Save Changes' : 'Create Project' }}</button>
                        @endif
                        <a href="{{ route('org.projects.index') }}" class="btn btn-link">Cancel</a>
                    </div>
                </form>
            </div>
            @if ($project->exists)
                <div class="tab-pane fade" id="amenities" role="tabpanel">
                    <form method="post" action="{{ route('org.projects.amenities.store') }}">
                        @csrf
                        <input type="hidden" id="projectId" name="project_id" value="{{ $project->id }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Amenities Name</label>
                                <select name="amenities_name" id="categorySelect" class="form-select select2"
                                    {{ $disabled }}>
                                    <option value="">--Select--</option>
                                    @foreach ($amenities as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3" style="margin-top: 41px;">
                                <button type="submit" class="btn btn-success">Save Amenities</button>
                            </div>

                            {{-- <div id="subCategoryContainer">
            </div> --}}

                        </div>


                    </form>
                    <div class="mt-3 card-body">
                        <h5>Amenities List</h5>
                        <table class="table table-striped align-middle" id="SubcategoryTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Amenities Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="floorPlan" role="tabpanel">
                    <form method="post"
                        action="{{ isset($typical) && $typical->exists
                            ? route('org.typical.units.update', $typical)
                            : route('org.typical.units.store') }}">

                        @csrf

                        {{-- Project ID --}}
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <div class="row g-3">

                            {{-- Bedrooms --}}
                            <div class="col-md-3">
                                <label class="form-label">rooms *</label>
                                <input type="number" name="bedrooms" step="0.5" class="form-control" required
                                    value="{{ old('bedrooms') }}">
                            </div>
                            <div class="col-md-3">
                                <label>Type</label>
                                <select name="type_name" id="type_name" class="form-select">
                                    <option value="">---Select Type---</option>

                                    @foreach ($types as $t)
                                        <option value="{{ $t->id }}">
                                            {{ $t->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>


                            <div class="col-md-3">
                                <label>Room Type</label>
                                <select name="room_type" id="room_type" class="form-select"
                                    data-old="{{ old('room_type') }}">
                                    <option value="">---Select Room Type---</option>
                                </select>
                            </div>
                            {{-- Currency --}}
                            <div class="col-md-3">
                                <label class="form-label">Currency *</label>
                                <select name="currency_code" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    @foreach ($countries as $ct)
                                        <option value="{{ $ct->country_currency }}"
                                            {{ old('currency_code', $typical->country_currency ?? '') == $ct->country_currency ? 'selected' : '' }}>
                                            {{ $ct->country_currency }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>

                            {{-- From Price --}}
                            <div class="col-md-3">
                                <label class="form-label">From Price</label>
                                <input type="number" step="0.01" name="from_price" class="form-control"
                                    value="{{ old('from_price') }}">
                            </div>

                            {{-- To Price --}}
                            <div class="col-md-3">
                                <label class="form-label">To Price</label>
                                <input type="number" step="0.01" name="to_price" class="form-control"
                                    value="{{ old('to_price') }}">
                            </div>

                            {{-- From Size Sqft --}}
                            <div class="col-md-3">
                                <label class="form-label">From Size (Sqft)</label>
                                <input type="number" step="0.01" name="from_size_sqft" class="form-control"
                                    value="{{ old('from_size_sqft') }}">
                            </div>

                            {{-- To Size Sqft --}}
                            <div class="col-md-3">
                                <label class="form-label">To Size (Sqft)</label>
                                <input type="number" step="0.01" name="to_size_sqft" class="form-control"
                                    value="{{ old('to_size_sqft') }}">
                            </div>

                            {{-- From Size m2 --}}
                            <div class="col-md-3">
                                <label class="form-label">From Size (m²)</label>
                                <input type="number" step="0.0000001" name="from_size_m2" class="form-control"
                                    value="{{ old('from_size_m2') }}">
                            </div>

                            {{-- To Size m2 --}}
                            <div class="col-md-3">
                                <label class="form-label">To Size (m²)</label>
                                <input type="number" step="0.0000001" name="to_size_m2" class="form-control"
                                    value="{{ old('to_size_m2') }}">
                            </div>

                            {{-- Sort Order --}}
                            <div class="col-md-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control"
                                    value="{{ old('sort_order', $typical->sort_order ?? 0) }}">
                            </div>
                            <input type="hidden" name="typical_id" value="{{ old('id') }}">


                        </div>

                        <div class="mt-4">
                            <button class="btn btn-primary">
                                Add Floor Plan
                            </button>
                            <a href="{{ route('org.projects.edit', $project->id) }}" class="btn btn-link">Cancel</a>
                        </div>
                    </form>
                    <div class="mt-3 card-body">
                        <h5>Floor Plan List</h5>
                        <table class="table table-striped align-middle" id="FloorPlanTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Type</th>
                                    <th>Room Type</th>
                                    <th>rooms</th>
                                    <th>Currency Code</th>
                                    <th>From Price </th>
                                    <th>To Price</th>
                                    <th>From Square Ft</th>
                                    <th>To Square Ft</th>
                                    <th>From SIze M2</th>
                                    <th>To SIze M2</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="projectMedia" role="tabpanel">
                    <form method="POST" action="{{ route('org.project-media.store') }}" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="project_id" value="{{ $project->id }}">

                        <div class="row g-3">

                            {{-- Collection --}}
                            <div class="col-md-3">
                                <label class="form-label">Collection</label>
                                <select name="collection" id="collection" class="form-select" required>
                                    <option value="">Select</option>
                                    <option value="cover">Cover</option>
                                    <option value="interior">Interior</option>
                                    <option value="architecture">Architecture</option>
                                    <option value="lobby">Lobby</option>
                                    <option value="general_plan">General Plan</option>
                                    <option value="brochure">Brochure</option>
                                    <option value="videos">Videos</option>
                                </select>
                            </div>

                            <div class="col-md-3" id="file_div">
                                <label class="form-label">Upload File</label>
                                <input type="file" name="file" class="form-control">
                            </div>

                            <div class="col-md-3" id="video_div" style="display:none;">
                                <label class="form-label">Video Link</label>
                                <input type="text" name="video_link" class="form-control"
                                    placeholder="Enter YouTube / Vimeo Link">
                            </div>

                            {{-- Sort Order --}}
                            <div class="col-md-2">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" value="0" class="form-control">
                            </div>



                            {{-- Submit --}}
                            <div class="col-md-2">
                                <div class="mt-4">
                                    <button class="btn btn-primary">
                                        Add Media
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>

                    <div class="mt-3 card-body">
                        <h5>Project Media List</h5>
                        <table class="table table-striped align-middle" id="ProjectMediaTable" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Collection</th>
                                    <th>File</th>
                                    <th>Size</th>
                                    <th>Width</th>
                                    <th>Height</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>

                <!----Payment Plan----->
                <div class="tab-pane fade" id="paymentPlan" role="tabpanel">
                    <form method="POST" action="{{ route('org.payment-plan.store') }}">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">

                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>Payment Plan</strong>
                            </div>

                            <div class="card-body row g-3">

                                <div class="col-md-4">
                                    <label class="form-label">Plan Name</label>
                                    <input type="text" name="plan_name" class="form-control" required>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Duration (Months)</label>
                                    <input type="number" name="duration_months" class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Is Handover?</label>
                                    <select name="is_handover" class="form-select">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Months After Handover</label>
                                    <input type="number" name="months_after_handover" class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">EOI</label>
                                    <input type="text" name="eoi" class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" value="0">
                                </div>

                            </div>

                            <div class="text-end p-3">
                                <button class="btn btn-success">Save Payment Plan</button>
                            </div>
                        </div>
                    </form>
                    <div class="mt-3 card-body">
                        <h5>Project Media List</h5>
                        <table class="table table-striped align-middle" id="PaymentPlanTable" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Duration Months</th>
                                    <th>Is Handover</th>
                                    <th>Months After Handover</th>
                                    <th>EOI</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>

                </div>

                <!----NearBy Plan----->
                <div class="tab-pane fade" id="NearByPlan" role="tabpanel">
                    <form method="POST" action="{{ route('org.near-by-plan.store') }}">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">

                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>Near By Plan</strong>
                            </div>
                            <div class="card-body row g-3">
                                <div class="col-md-4">
                                    <label class="form-label"> Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Distance</label>
                                    <input type="number" name="distance" min="0" step="0.01"
                                        class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Time</label>
                                    <input type="number" name="time" min="0" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" min="0" class="form-control"
                                        value="0">
                                </div>
                                <div class="text-end p-3 col-md-2 " style="margin-top: 28px">
                                    <button class="btn btn-success">Save Near Plan</button>
                                </div>
                            </div>

                        </div>
                    </form>
                    <div class="mt-3 card-body">
                        <h5>Near By List</h5>
                        <table class="table table-striped align-middle" id="NearByPlanTable" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Distance</th>
                                    <th>Time</th>
                                    <th>Sort</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>



    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {

            // --- Form 1: Project Save ---
            $('#projectForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        alert('Project saved successfully!');
                        // Optionally switch to amenities tab after success
                        $('#amenities-tab').tab('show');
                    },
                    error: function() {
                        alert('Error saving project');
                    }
                });
            });

            // $('#categorySelect').on('change', function() {
            //   var catId = $(this).val();
            //   var projectId =  $('#projectId').val();
            //   var catText = $('#categorySelect option:selected').text();

            //   if (catId) {
            //       if ($('#category_block_' + catId).length) {
            //           alert(catText + " subcategories already loaded!");
            //           return;
            //       }

            //       $.ajax({
            //           // url: "{{ url('org-admin/get-subcategories') }}/" + catId,
            //            url: "{{ url('org-admin/get-subcategories') }}/" + catId + "/" + projectId,
            //           method: 'GET',
            //           success: function(res) {
            //               var html = `
        //                   <div class="border rounded p-3 mb-3" id="category_block_${catId}">
        //                       <div class="d-flex justify-content-between align-items-center mb-2">
        //                           <h6 class="mb-0">${catText}</h6>
        //                           <button type="button" class="btn btn-sm btn-outline-danger remove-category" data-id="${catId}">
        //                               Remove
        //                           </button>
        //                       </div>
        //                       ${res}
        //                   </div>
        //               `;
            //               $('#subCategoryContainer').append(html);
            //           },
            //           error: function() {
            //               alert('Error loading subcategories.');
            //           }
            //       });
            //   }
            // });

            $(document).on('click', '.remove-category', function() {
                var id = $(this).data('id');
                $('#category_block_' + id).remove();
            });


            $('#amenitiesForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        alert('Amenities saved successfully!');
                    },
                    error: function() {
                        alert('Error saving amenities');
                    }
                });
            });


            $('#SubcategoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('org.project.subcategory.get') }}",
                    type: 'GET',
                    data: function(d) {
                        d.project_id = {{ $project->id ?? 'null' }};
                    }
                },
                columns: [{
                        data: 'sn',
                        name: 'sn',
                        orderable: false
                    },
                    {
                        data: 'amenity_id',
                        name: 'amenity_id'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                pageLength: 10
            });


            $('#FloorPlanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('org.typical.units.get') }}",
                    type: 'GET',
                    data: function(d) {
                        d.project_id = {{ $project->id ?? 'null' }};
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false
                    },
                    {
                        data: 'type_name'
                    },
                    {
                        data: 'room_type'
                    },
                    {
                        data: 'bedrooms',
                        name: 'bedrooms'
                    },
                    {
                        data: 'currency_code',
                        name: 'currency_code'
                    },
                    {
                        data: 'from_price',
                        orderable: false
                    },
                    {
                        data: 'to_price',
                        orderable: false
                    },
                    {
                        data: 'from_size_sqft',
                        orderable: false
                    },
                    {
                        data: 'to_size_sqft',
                        orderable: false
                    },
                    {
                        data: 'from_size_m2',
                        orderable: false
                    },
                    {
                        data: 'to_size_m2',
                        orderable: false
                    },
                    {
                        data: 'action',
                        orderable: false
                    },
                ],
                order: [
                    [1, 'asc']
                ],
                pageLength: 10
            });
            $('#room_type').on('change', function() {

                if ($(this).val() === 'Other') {
                    $('#other_room_div').show();
                } else {
                    $('#other_room_div').hide();
                    $('#other_room_type').val('');
                }

            });

            $(document).ready(function() {

                var oldType = $('#type_name').val() || '';
                var oldRoom = $('#room_type').data('old') || '';

                if (oldType !== '') {
                    loadRoom(oldType, oldRoom);
                }


                $('#type_name').on('change', function() {

                    var id = $(this).val();

                    if (id) {
                        loadRoom(id, '');
                    } else {
                        $('#room_type').html('<option value="">Select</option>');
                    }

                });


                function loadRoom(type_id, selected) {

                    $('#room_type').html('<option>Loading...</option>');

                    $.ajax({

                        url: "{{ route('org.get-room-types') }}",

                        type: "GET",

                        data: {
                            type_id: type_id
                        },

                        success: function(res) {

                            $('#room_type').empty();

                            $('#room_type').append(
                                '<option value="">Select</option>'
                            );

                            $.each(res, function(k, v) {

                                var sel =
                                    selected == v.name ?
                                    'selected' :
                                    '';

                                $('#room_type').append(
                                    '<option value="' + v.name + '" ' + sel + '>' +
                                    v.name +
                                    '</option>'
                                );

                            });

                        }

                    });

                }

            });


            $('#ProjectMediaTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('org.project-media.get') }}",
                    type: 'GET',
                    data: function(d) {
                        d.project_id = {{ $project->id ?? 'null' }};
                    }
                },
                columns: [{
                        data: 'id',
                        orderable: false
                    },
                    {
                        data: 'collection'
                    },
                    {
                        data: 'url',
                        orderable: false
                    },
                    {
                        data: 'size',
                        orderable: false
                    },
                    {
                        data: 'width',
                        orderable: false
                    },
                    {
                        data: 'height',
                        orderable: false
                    },
                    {
                        data: 'created_at',
                        render: function(data) {
                            if (!data) return '-';
                            let d = new Date(data * 1000);
                            return d.toLocaleDateString('en-GB', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });
                        }
                    },
                    {
                        data: 'action',
                        orderable: false
                    },
                ],
                order: [
                    [7, 'desc']
                ],
                pageLength: 10
            });

            $('#PaymentPlanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('org.payment-plan.get') }}",
                    type: 'GET',
                    data: function(d) {
                        d.project_id = {{ $project->id ?? 'null' }};
                    }
                },
                columns: [{
                        data: 'id',
                        orderable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'duration_months'
                    },
                    {
                        data: 'is_handover',
                        orderable: false
                    },
                    {
                        data: 'months_after_handover',
                        orderable: false
                    },
                    {
                        data: 'eoi',
                        orderable: false
                    },
                    {
                        data: 'action',
                        orderable: false
                    },
                ],
                order: [
                    [1, 'asc']
                ], // name se order
                pageLength: 10
            });


            $('#NearByPlanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('org.near-by-plan.get') }}",
                    type: 'GET',
                    data: function(d) {
                        d.project_id = {{ $project->id ?? 'null' }};
                    }
                },
                columns: [{
                        data: 'id',
                        orderable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'distance'
                    },
                    {
                        data: 'time'
                    },
                    {
                        data: 'sort_order'
                    },
                    {
                        data: 'action',
                        orderable: false
                    },
                ],
                order: [
                    [1, 'asc']
                ], // name se order
                pageLength: 10
            })





            function loadDevelopers(countryId, selectedDeveloper = '') {
                $('#developer_id')
                    .html('<option>Loading developers...</option>')
                    .prop('disabled', true)
                    .trigger('change');

                $.ajax({
                    url: "{{ url('org-admin/get/countriesdeveloper') }}",
                    type: "POST",
                    data: {
                        country_id: countryId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        let options = '<option value="">-- Select Developer --</option>';

                        if (res.length > 0) {
                            $.each(res, function(i, dev) {
                                let selected = (dev.id == selectedDeveloper) ? 'selected' : '';
                                options +=
                                    `<option value="${dev.id}" ${selected}>${dev.name}</option>`;
                            });
                        }

                        $('#developer_id')
                            .html(options)
                            .prop('disabled', false)
                            .trigger('change');
                    }
                });
            }
            $('#country_id').on('change', function() {
                let countryId = $(this).val();
                loadDevelopers(countryId);
            });
            $('.select2').select2({
                width: '100%'
            });
            let countryId = $('#country_id').val();
            let selectedDeveloper = $('#developer_id').data('selected');

            if (countryId) {
                loadDevelopers(countryId, selectedDeveloper);
            }




            var currentPage = window.location.pathname;
            var lastPage = localStorage.getItem('project_tab_page');
            var activeTab = localStorage.getItem('project_active_tab');

            // Same page + tab saved → wahi tab active
            if (lastPage === currentPage && activeTab) {
                $('#projectTabs button[data-tab="' + activeTab + '"]').tab('show');
            } else {
                // Dusre module se aaye → Project Details
                localStorage.setItem('project_active_tab', 'details');
                localStorage.setItem('project_tab_page', currentPage);
                $('#details-tab').tab('show');
            }

            // Tab change hote hi save karo
            $('#projectTabs button').on('shown.bs.tab', function() {
                var tabName = $(this).data('tab');
                localStorage.setItem('project_active_tab', tabName);
                localStorage.setItem('project_tab_page', currentPage);
            });



            $('#materialType').on('change', function() {

                let type = $(this).val();

                $('#fileBox, #urlBox').addClass('d-none');

                if (type === 'pdf' || type === 'image') {
                    $('#fileBox').removeClass('d-none');
                }

                if (type === 'video' || type === 'link') {
                    $('#urlBox').removeClass('d-none');
                }
            });





        });
    </script>

    <script>
        $(document).ready(function() {

            function toggleInput() {
                var val = $('#collection').val();

                if (val == 'videos') {
                    $('#file_div').hide();
                    $('#video_div').show();
                } else {
                    $('#file_div').show();
                    $('#video_div').hide();
                }
            }

            toggleInput(); // page load par

            $('#collection').change(function() {
                toggleInput();
            });


            //slug work

            function toggleSlug() {
                if ($('#autoSlug').is(':checked')) {
                    $('#slugInput').val("{{ $project->slug }}"); // real slug show
                    $('#slugInput').prop('disabled', true);
                } else {
                    $('#slugInput').prop('disabled', false);
                }
            }

            // page load par check
            toggleSlug();

            // checkbox change par
            $('#autoSlug').change(function() {
                toggleSlug();
            });

            function loadSubCategory(type, selectedSubCategory = null) {

                if(type != '') {
                    $.ajax({
                        url: "{{ route('org.get.unit_type.amenities') }}",
                        type: 'GET',
                        data: { type: type },
                        success: function (response) {

                            let html = '<option value="">----Select---</option>';

                            response.forEach(function (item) {

                                let selected = (selectedSubCategory == item.id) ? 'selected' : '';

                                html += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                            });

                            $('#sub_category').html(html);
                        }
                    });
                }
            }

            // 🔥 change event
            $('#project_type').on('change', function () {
                let type = $(this).val();
                loadSubCategory(type);
            });

            // 🔥 EDIT MODE (page load pe call)
            let type = $('#project_type').val();
            let selectedSubCategory = "{{ $project->sub_category ?? '' }}";

            if(type) {
                loadSubCategory(type, selectedSubCategory);
            }





        });






    </script>
@endsection
