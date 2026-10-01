@extends('layouts.frontend.frontend_layout')
@section('main-content')
    <style>
        .images-style {
            width: 100%;
            height: 220px;
            overflow: hidden;
            border-radius: 8px;
        }

        .images-style img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .image-cap {
            width: 100%;
            height: 100%;
            background: #6c757d;
            /* grey */
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 500;
        }

        .developer-about {
            padding: 70px 0 30px;
            background: #fff;
        }

        .developer-about__grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 60px;
            align-items: center;
            /* align logo nicely */
        }

        /* LEFT */
        .developer-about__title {
            font-size: 48px;
            font-weight: 800;
            color: #0b2a4a;
            margin-bottom: 16px;
        }

        .developer-about__text {
            font-size: 18px;
            line-height: 1.9;
            color: #333;
            max-width: 750px;
        }

        /* RIGHT LOGO WRAPPER */
        .developer-about__logo {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* REAL LOGO */
        .developer-about__logo img {
            max-width: 420px;
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        /* PLACEHOLDER FIX (not full width anymore) */
        .logo-placeholder {
            width: 420px;
            height: 140px;
            background: #6c757d;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 22px;
            font-weight: 500;
        }

        /* Divider */
        .developer-about__divider {
            margin-top: 50px;
            height: 1px;
            background: #dbe3ef;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .developer-about__grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .developer-about__logo {
                justify-content: flex-start;
            }

            .logo-placeholder {
                width: 100%;
            }
        }    
        

        .single-line-ellipsis {
            display: block;        /* important */
            width: 100%;           /* container ke according */
            white-space: nowrap;   /* line break band */
            overflow: hidden;      
            text-overflow: ellipsis;
        }

        .two-line{
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 22px;
            min-height: 44px; /* 2 lines ka space */
        }



/*location dropdown*/


.location-dropdown{
    position:absolute;
    width:100%;
    background:#fff;
    border:1px solid #ddd;
    max-height:200px;
    overflow-y:auto;
    display:none;
    z-index:999;
}

.location-item{
    padding:8px 10px;
    cursor:pointer;
}

.location-item:hover{
    background:#f5f5f5;
}

#selected_locations{
    display:flex;
    gap:6px;
    margin-top:4px;
    max-height:30px;
    overflow-x:auto;
    overflow-y:hidden;
    white-space:nowrap;
}

.location-tag{
    background:#eee;
    padding:4px 8px;
    border-radius:4px;
    font-size:12px;
    display:inline-flex;
    align-items:center;
}

.remove-location{
    margin-left:5px;
    cursor:pointer;
}
.remove-location:hover{
    color:red;
}


    </style>
    <section class="flat-map">
        {{-- <div id="map" class="top-map" data-map-zoom="16" data-map-scroll="true"></div> --}}
        <div class="container" style="margin-top: 110px;">
            <div class="wrap-filter-search">
                <div class="flat-tab flat-tab-form">
                    {{-- <ul class="nav-tab-form style-3 justify-content-center" role="tablist">
                        <li class="nav-tab-item" role="presentation">
                            <a href="#forRent" class="nav-link-item active" data-bs-toggle="tab">For Rent</a>
                        </li>
                        <li class="nav-tab-item" role="presentation">
                            <a href="#forSale" class="nav-link-item" data-bs-toggle="tab">For Sale</a>
                        </li>
                    </ul> --}}
                    <div class="tab-content">
                        <div class="tab-pane fade active show" role="tabpanel">
                            <div class="form-sl">
                                <form method="get" action="{{ route('project') }}">
                                    <div class="wd-find-select shadow-st">
                                        <div class="inner-group">
                                            <div class="form-group-1 search-form form-style">
                                                <label>Keyword</label>
                                                <input type="text" name="keyword" value="{{ request('keyword') }}"
                                                    class="form-control" placeholder="Search Keyword." title="Search for">
                                            </div>
                                            <div class="form-group-2 form-style">
                                                <label>Location</label>
                                                <div class="group-ip position-relative">
                                                    <input type="text" id="location_search" class="form-control"
                                                        placeholder="Search Location">

                                                    <div id="location_dropdown" class="location-dropdown"></div>
                                                    
                                                    <div id="selected_locations">
                                                         @if(request()->location)
                                                            @foreach(request()->location as $loc)
                                                                <span class="location-tag">
                                                                    {{ $loc }}
                                                                    <i class="remove-location">✖</i>
                                                                    <input type="hidden" name="location[]" value="{{ $loc }}">
                                                                </span>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group-3 form-style">
                                                <label>Type</label>
                                                <div class="group-select">
                                                    <select name="type" class="form-control">
                                                        <option value=""
                                                            {{ request('type') == '' ? 'selected' : '' }}>
                                                            All</option>
                                                        <option value="villa"
                                                            {{ request('type') == 'villa' ? 'selected' : '' }}>Villa
                                                        </option>
                                                        <option value="studio"
                                                            {{ request('type') == 'studio' ? 'selected' : '' }}>Studio
                                                        </option>
                                                        <option value="office"
                                                            {{ request('type') == 'office' ? 'selected' : '' }}>Office
                                                        </option>
                                                        <option value="house"
                                                            {{ request('type') == 'house' ? 'selected' : '' }}>House
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- <div class="form-group-4 box-filter">
                                                <a class="filter-advanced pull-right">
                                                    <span class="icon icon-faders"></span>
                                                    <span class="text-1">Advanced</span>
                                                </a>
                                            </div> --}}
                                        </div>
                                        <button type="submit" class="tf-btn primary" href="#">Search</button>
                                    </div>
                                    <div class="wd-search-form">
                                        <div class="grid-2 group-box group-price">
                                            <div class="widget-price">
                                                <div class="box-title-price">
                                                    <span class="title-price">Price Range</span>
                                                    <div class="caption-price">
                                                        <span>from</span>
                                                        <span id="slider-range-value1" class="fw-7"></span>
                                                        <span>to</span>
                                                        <span id="slider-range-value2" class="fw-7"></span>
                                                    </div>
                                                </div>
                                                <div id="slider-range"></div>
                                                <div class="slider-labels">
                                                    <div>
                                                        <input type="hidden" name="min-value" value="">
                                                        <input type="hidden" name="max-value" value="">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="widget-price">
                                                <div class="box-title-price">
                                                    <span class="title-price">Size Range</span>
                                                    <div class="caption-price">
                                                        <span>from</span>
                                                        <span id="slider-range-value01" class="fw-7"></span>
                                                        <span>to</span>
                                                        <span id="slider-range-value02" class="fw-7"></span>
                                                    </div>
                                                </div>
                                                <div id="slider-range2"></div>
                                                <div class="slider-labels">
                                                    <div>
                                                        <input type="hidden" name="min-value2" value="">
                                                        <input type="hidden" name="max-value2" value="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid-2 group-box">
                                            <div class="group-select grid-2">
                                                <div class="box-select">
                                                    <label class="title-select text-variant-1">Rooms</label>
                                                    <div class="nice-select" tabindex="0"><span class="current">2</span>
                                                        <ul class="list">
                                                            <li data-value="1" class="option">1</li>
                                                            <li data-value="2" class="option selected">2</li>
                                                            <li data-value="3" class="option">3</li>
                                                            <li data-value="4" class="option">4</li>
                                                            <li data-value="5" class="option">5</li>
                                                            <li data-value="6" class="option">6</li>
                                                            <li data-value="7" class="option">7</li>
                                                            <li data-value="8" class="option">8</li>
                                                            <li data-value="9" class="option">9</li>
                                                            <li data-value="10" class="option">10</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="box-select">
                                                    <label class="title-select text-variant-1">Bathrooms</label>
                                                    <div class="nice-select" tabindex="0"><span
                                                            class="current">2</span>
                                                        <ul class="list">
                                                            <li data-value="1" class="option">1</li>
                                                            <li data-value="2" class="option selected">2</li>
                                                            <li data-value="3" class="option">3</li>
                                                            <li data-value="4" class="option">4</li>
                                                            <li data-value="5" class="option">5</li>
                                                            <li data-value="6" class="option">6</li>
                                                            <li data-value="7" class="option">7</li>
                                                            <li data-value="8" class="option">8</li>
                                                            <li data-value="9" class="option">9</li>
                                                            <li data-value="10" class="option">10</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="group-select grid-2">
                                                <div class="box-select">
                                                    <label class="title-select text-variant-1">Bedrooms</label>
                                                    <div class="nice-select" tabindex="0"><span
                                                            class="current">2</span>
                                                        <ul class="list">
                                                            <li data-value="1" class="option">1</li>
                                                            <li data-value="2" class="option selected">2</li>
                                                            <li data-value="3" class="option">3</li>
                                                            <li data-value="4" class="option">4</li>
                                                            <li data-value="5" class="option">5</li>
                                                            <li data-value="6" class="option">6</li>
                                                            <li data-value="7" class="option">7</li>
                                                            <li data-value="8" class="option">8</li>
                                                            <li data-value="9" class="option">9</li>
                                                            <li data-value="10" class="option">10</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="box-select">
                                                    <label class="title-select fw-5">Type</label>
                                                    <div class="nice-select" tabindex="0"><span
                                                            class="current">2</span>
                                                        <ul class="list">
                                                            <li data-value="1" class="option">1</li>
                                                            <li data-value="2" class="option selected">2</li>
                                                            <li data-value="3" class="option">3</li>
                                                            <li data-value="4" class="option">4</li>
                                                            <li data-value="5" class="option">5</li>
                                                            <li data-value="6" class="option">6</li>
                                                            <li data-value="7" class="option">7</li>
                                                            <li data-value="8" class="option">8</li>
                                                            <li data-value="9" class="option">9</li>
                                                            <li data-value="10" class="option">10</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="group-checkbox">
                                            <div class="text-1">Amenities:</div>
                                            <div class="group-amenities mt-8 grid-6">
                                                <div class="box-amenities">
                                                    <fieldset class="amenities-item">
                                                        <input type="checkbox" class="tf-checkbox style-1" id="cb1"
                                                            checked>
                                                        <label for="cb1" class="text-cb-amenities">Air
                                                            Condition</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb2">
                                                        <label for="cb2" class="text-cb-amenities">Cable TV</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb3">
                                                        <label for="cb3" class="text-cb-amenities">Ceiling
                                                            Height</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb4">
                                                        <label for="cb4" class="text-cb-amenities">Fireplace</label>
                                                    </fieldset>
                                                </div>
                                                <div class="box-amenities">
                                                    <fieldset class="amenities-item">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb5">
                                                        <label for="cb5" class="text-cb-amenities">Disabled
                                                            Access</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1" id="cb6"
                                                            checked>
                                                        <label for="cb6" class="text-cb-amenities">Elevator</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb7">
                                                        <label for="cb7" class="text-cb-amenities">Fence</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb8">
                                                        <label for="cb8" class="text-cb-amenities">Garden</label>
                                                    </fieldset>
                                                </div>
                                                <div class="box-amenities">
                                                    <fieldset class="amenities-item">
                                                        <input type="checkbox" class="tf-checkbox style-1" id="cb9"
                                                            checked>
                                                        <label for="cb9" class="text-cb-amenities">Floor</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb10">
                                                        <label for="cb10" class="text-cb-amenities">Furnishing</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1" id="cb11"
                                                            checked>
                                                        <label for="cb11" class="text-cb-amenities">Garage</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb12">
                                                        <label for="cb12" class="text-cb-amenities">Pet
                                                            Friendly</label>
                                                    </fieldset>
                                                </div>
                                                <div class="box-amenities">
                                                    <fieldset class="amenities-item">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb13">
                                                        <label for="cb13" class="text-cb-amenities">Heating</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb14">
                                                        <label for="cb14" class="text-cb-amenities">Intercom</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb15">
                                                        <label for="cb15" class="text-cb-amenities">Parking</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb16">
                                                        <label for="cb16" class="text-cb-amenities">WiFi</label>
                                                    </fieldset>
                                                </div>
                                                <div class="box-amenities">
                                                    <fieldset class="amenities-item">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb17">
                                                        <label for="cb17" class="text-cb-amenities">Renovation</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb18">
                                                        <label for="cb18" class="text-cb-amenities">Security</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb19">
                                                        <label for="cb19" class="text-cb-amenities">Swimming
                                                            Pool</label>
                                                    </fieldset>

                                                </div>
                                                <div class="box-amenities">
                                                    <fieldset class="amenities-item">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb20">
                                                        <label for="cb20" class="text-cb-amenities">Window
                                                            Type</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb21">
                                                        <label for="cb21" class="text-cb-amenities">Search
                                                            property</label>
                                                    </fieldset>
                                                    <fieldset class="amenities-item mt-12">
                                                        <input type="checkbox" class="tf-checkbox style-1"
                                                            id="cb22">
                                                        <label for="cb22" class="text-cb-amenities">Construction
                                                            Year</label>
                                                    </fieldset>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    @if ($dev_data)     
        <section class="developer-about">
            <div class="container">
                <div class="developer-about__grid">

                    {{-- LEFT CONTENT --}}
                    <div class="developer-about__content">
                        <h2 class="developer-about__title">
                            {{ $dev_data->name ?? 'Emaar Properties' }}
                        </h2>

                        <p class="developer-about__text">
                            {{ $dev_data->description ?? 'One of the pioneer real estate developers in Dubai...' }}
                        </p>
                    </div>

                    {{-- RIGHT LOGO --}}
                    <div class="developer-about__logo">
                         @php
                            $defaultImage = asset('assets/images/defalt/dummy_property_images.png');
                            $imagePath = $defaultImage;

                            if (!empty($dev_data->logo_path) && file_exists(public_path('storage/'.$dev_data->logo_path))) {
                                $imagePath = asset('storage/'.$dev_data->logo_path);
                            }
                        @endphp
                        <img src="{{ $imagePath }}" alt="img" style="object-fit: contain;">
                        {{-- @if (!empty($dev_data->logo))
                            <img src="{{ asset($dev_data->logo) }}" alt="Developer Logo">
                        @else
                            <div class="logo-placeholder">
                                Image cap
                            </div>
                        @endif --}}
                    </div>

                </div>

                <div class="developer-about__divider"></div>
            </div>
        </section>
    @endif


    <section class="flat-section-v5 flat-recommended flat-recommended-v2" style="{{ $dev_data != null ? 'margin-top: -130px;' : 'margin-top: -55px;' }}">
        <div class="container">
            <div class="box-title-listing">
                @if (!$dev_data)
                <h1> Projects In {{ $countries->country_name == 'India' ? 'India' : 'UAE' }} </h1>

                @endif
                <div class="box-filter-tab">
                    <ul class="nav-tab-filter" role="tablist">
                        <li class="nav-tab-item" role="presentation">
                            <a href="#gridLayout" class="nav-link-item active" data-bs-toggle="tab"><i
                                    class="icon icon-grid"></i></a>
                        </li>
                        <li class="nav-tab-item" role="presentation">
                            <a href="#listLayout" class="nav-link-item" data-bs-toggle="tab"><i
                                    class="icon icon-list"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade active show" id="gridLayout" role="tabpanel">
                    <div class="row">
                        @foreach ($data as $d)
                            @php
                                $p = $d['project'];
                                $media = $d['media_url'] ?? null;
                                $bedrooms = $d['technical_units']->pluck('bedrooms')->unique()->sort()->values();
                            @endphp

                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="homeya-box">
                                    <div class="archive-top">

                                        {{-- IMAGE --}}
                                        <a href="{{ route('project-details', $p->slug) }}" class="images-group">
                                            <div class="images-style">
                                                {{-- <img src="{{ $media ? asset($media) : asset('assets/images/defalt/dummy_property_images.png') }}" alt="img"> --}}
                                                <img 
                                                    src="{{ $media ? asset($media) : asset('assets/images/defalt/dummy_property_images.png') }}" 
                                                    onerror="this.onerror=null;this.src='{{ asset('assets/images/defalt/dummy_property_images.png') }}';"
                                                    alt="img">
                                            </div>
                                            <div class="top">
                                                <ul class="d-flex gap-8">
                                                     @if($p->featured)<li class="flag-tag success">Featured</li>@endif
                                                    @if($p->sale_status)
                                                    <li class="flag-tag style-1">{{ str_replace('_', ' ', $p->sale_status) }}</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </a>

                                        <div class="content">
                                            <div class="h7 text-capitalize fw-7">
                                                <a href="{{ route('project-details', $p->slug) }}" class="link single-line-ellipsis">
                                                    {{ $p->name }}
                                                </a>
                                            </div>
                                           @php
                                                $countryCode = session('country_code'); 
                                                if ($countryCode === 'in') {
                                                    $currency = '₹'; 
                                                } else {
                                                    $currency = 'AED';
                                                   
                                                }
                                            @endphp

                                            <div class="desc">
                                                <p style="font-weight:900 !important;">
                                                    Starting Price 
                                                    <span style="color:#ED2027;">
                                                        {{ $currency }} {{ priceShort($p->min_price, 0) }}
                                                    </span>
                                                </p>
                                            </div>


                                            <div class="desc">
                                                <i class="fs-16 icon icon-mapPin"></i>
                                                <p class="two-line">
    {{ $p->locality }}{{ $p->locality && $p->city ? ', ' : '' }}{{ $p->city }}
</p>
                                            </div>
                                            <ul class="meta-list">
                                                <li class="item">
                                                    <i class="icon icon-bed"></i>
                                                    <span>
                                                        @foreach ($bedrooms as $b)
                                                            {{ $b == 0 ? '' : $b }}@if (!$loop->last)
                                                                @if ($b != 0)
                                                                    ,
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $paginator->onEachSide(1)->links('pagination.wd-navigation') }}
                    </div>

                </div>

            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        $(function() {
            $('select[name="type"]').niceSelect();

$('#location_search').keyup(function () {
    let keyword = $(this).val().trim();

    if (keyword.length < 2) {
        $('#location_dropdown').hide().html('');
        return;
    }

    $.ajax({
        url: "{{ route('search.location') }}",
        type: "GET",
        data: { keyword: keyword },
        success: function (data) {
            let html = '';

            if (data.length > 0) {
                data.forEach(function (item) {
                    let locality = (item.locality || '').trim();
                    let city = (item.city || '').trim();
                    let locationText = [locality, city].filter(Boolean).join(' | ');

                    html += `
                        <div class="location-item"
                             data-locality="${locality}"
                             data-city="${city}"
                             data-value="${locationText}">
                            ${locationText}
                        </div>
                    `;
                });

                $('#location_dropdown').html(html).show();
            } else {
                $('#location_dropdown').html('<div class="location-item">No location found</div>').show();
            }
        }
    });
});


           $(document).on('click','.location-item',function(){
                var location = $(this).text();
                var exists = false;
                $('input[name="location[]"]').each(function(){
                    if($(this).val() == location){
                        exists = true;
                    }
                });
                if(exists){
                    $('#location_dropdown').hide();
                    $('#location_search').val('');
                    return;
                }
                $('#selected_locations').append(
                    '<span class="location-tag">'+location+
                    ' <i class="remove-location">✖</i>'+
                    '<input type="hidden" name="location[]" value="'+location+'"></span>'
                );

                $('#location_dropdown').hide();
                $('#location_search').val('');
            });
            $(document).on('click','.remove-location',function(){
                $(this).closest('.location-tag').remove();
            });
        });
    </script>
@endsection
