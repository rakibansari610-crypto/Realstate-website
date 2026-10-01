
@extends('layouts.frontend.frontend_layout')
@section('main-content')
<style>
    .agent-img{
        height: 271px;
    }


    /* Limit testimonial text to 3 lines */
.testimonial-text {
    display: -webkit-box;
    -webkit-line-clamp: 3;   
    -webkit-box-orient: vertical;
    overflow: hidden;
}
 
.box-tes-item {
    min-height: 260px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}


.two-line {
    display: -webkit-box;
    -webkit-line-clamp: 2;   /* show only 2 lines */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}


.property-text {
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

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

.single-line-ellipsis {
    display: block;        /* important */
    width: 100%;           /* container ke according */
    white-space: nowrap;   /* line break band */
    overflow: hidden;      
    text-overflow: ellipsis;
}

.list-view {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 40px 60px; /* vertical & horizontal spacing */
    list-style: none;
    padding: 0;
}

.list-view li {
    display: flex;
    align-items: flex-start;
}

.item-wrapper {
    display: flex;
    align-items: flex-start;
}

.check-icon {
    flex-shrink: 0;
    margin-right: 14px;
    margin-top: 5px;
}

.content {
    display: flex;
    flex-direction: column;
}

.list-title {
    font-size: 18px;
    font-weight: 700;
    color: #000;
    margin-bottom: 6px;
    line-height: 1.3;
}

.list-desc {
    margin: 0;
    font-size: 15px;
    color: #555;
    line-height: 1.6;
}

/* Important Fix */
.list-view {
    align-items: start;
}

/* Mobile */
@media (max-width: 768px) {
    .list-view {
        grid-template-columns: 1fr;
    }
}
.flat-slider.home-2 .slider-content .heading .title
{
    line-height:65px;
    font-size:3.5rem;
}
.flat-slider.home-2 .slider-content
{
    padding-top:85px;
}

</style>
    <!-- Slider -->
    <section class="flat-slider home-2">
        <div class="container relative">
            <div class="row">
                <div class="col-xl-10">
                    <div class="slider-content">
                       
                        <div class="heading">
                            <h1 class="title wow fadeIn animationtext clip" data-wow-delay=".2s" data-wow-duration="2000ms">
                              Make the Right    Property Move
                                
                            </h1>
                            <p class="subtitle body-1 wow fadeIn" data-wow-delay=".8s" data-wow-duration="2000ms">Helping you buy or rent the right space with clear guidance.</p>
                        </div>
                        <div class="flat-tab flat-tab-form">
                            <ul class="nav-tab-form style-2" role="tablist">
                                
                                <li class="nav-tab-item" role="presentation">
                                    <a href="#forSale" data-purpose="sale" class="nav-link-item active" data-bs-toggle="tab">For Sale</a>
                                </li>
                                <li class="nav-tab-item" role="presentation">
                                    <a href="#forRent" data-purpose="rent" class=" nav-link-item " data-bs-toggle="tab">For Rent</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade active show" role="tabpanel">
                                    <div class="form-sl">
                                      
                                        <form method="GET" action="{{ route('properties_search') }}" id="homeSearchForm">
                                            <input type="hidden" name="purpose" id="property_purpose">
                                            {{-- hidden fields (JS se fill honge) --}}
                                            <input type="hidden" name="unit_type" id="filter_unit_type">
                                            <input type="hidden" name="amenities" id="filter_amenities">

                                            <div class="wd-find-select style-2 shadow-st no-left-round">
                                                <div class="inner-group">

                                                    {{-- KEYWORD --}}
                                                    <div class="form-group-1 search-form form-style">
                                                        <label>Keyword</label>
                                                        <input type="text" class="form-control" name="keyword"
                                                            placeholder="Search Keyword" value="{{ request('keyword') }}">
                                                    </div>

                                                    {{-- LOCATION --}}
                                                    <div class="form-group-2 form-style">
                                                        <label>Location</label>
                                                        <div class="group-ip">
                                                            <input type="text" class="form-control" name="location"
                                                                placeholder="Search Location"
                                                                value="{{ request('location') }}">
                                                            <a href="#" class="icon icon-location"></a>
                                                        </div>
                                                    </div>

                                                    {{-- UNIT TYPE (DYNAMIC) --}}
                                                    <div class="form-group-3 form-style">
                                                        <label>Type</label>
                                                        <div class="group-select">
                                                            <div class="nice-select js-unit-type" tabindex="0">
                                                                <span class="current">All</span>
                                                                <ul class="list">
                                                                    <li data-value="" class="option selected">All</li>
                                                                    @foreach ($unitTypes as $type)
                                                                        <li data-value="{{ $type->id }}" class="option">
                                                                            {{ $type->name }}
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- ADVANCED --}}
                                                    <div class="form-group-4 box-filter">
                                                        <a class="filter-advanced pull-right">
                                                            <span class="icon icon-faders"></span>
                                                            <span class="text-advanced">Advanced</span>
                                                        </a>
                                                    </div>

                                                </div>

                                                <button type="submit" class="tf-btn primary">Search</button>
                                            </div>

                                            {{-- ================= ADVANCED FILTER ================= --}}
                                            <div class="wd-search-form">

                                                {{-- PRICE + SIZE --}}
                                                <div class="grid-2 group-box group-price">
                                                    <div class="widget-price">
                                                        <div class="box-title-price">
                                                            <span class="title-price">Price Range</span>
                                                        </div>
                                                        <div id="slider-range"></div>
                                                        <input type="hidden" name="min_price" id="min_price">
                                                        <input type="hidden" name="max_price" id="max_price">
                                                    </div>

                                                    <div class="widget-price">
                                                        <div class="box-title-price">
                                                            <span class="title-price">Size Range</span>
                                                        </div>
                                                        <div id="slider-range2"></div>
                                                        <input type="hidden" name="min_size" id="min_size">
                                                        <input type="hidden" name="max_size" id="max_size">
                                                    </div>
                                                </div>

                                                {{-- ROOMS / BATH / BED --}}
                                                <div class="grid-2 group-box">
                                                    <div class="group-select grid-2">
                                                        <div class="box-select">
                                                            <label>Bedrooms</label>
                                                            <select name="bedrooms" class="form-control">
                                                                <option value="">Any</option>
                                                                @for ($i = 1; $i <= 10; $i++)
                                                                    <option value="{{ $i }}">{{ $i }}
                                                                    </option>
                                                                @endfor
                                                            </select>
                                                        </div>

                                                        <div class="box-select">
                                                            <label>Bathrooms</label>
                                                            <select name="bathrooms" class="form-control">
                                                                <option value="">Any</option>
                                                                @for ($i = 1; $i <= 10; $i++)
                                                                    <option value="{{ $i }}">{{ $i }}
                                                                    </option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ================= AMENITIES (DYNAMIC) ================= --}}
                                                <div class="group-checkbox">
                                                    <div class="text-1">Amenities:</div>

                                                    <div class="group-amenities mt-8 grid-6">
                                                        @foreach ($amenities as $amenity)
                                                            <fieldset class="amenities-item">
                                                                <input type="checkbox"
                                                                    class="tf-checkbox style-1 js-amenity"
                                                                    value="{{ $amenity->id }}"
                                                                    id="amenity_{{ $amenity->id }}">

                                                                <label for="amenity_{{ $amenity->id }}"
                                                                    class="text-cb-amenities">
                                                                    {{ $amenity->name }}
                                                                </label>
                                                            </fieldset>
                                                        @endforeach
                                                    </div>
                                                </div>

                                            </div>
                                        </form>

                                        <!-- End Job  Search Form-->
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="wrap-search-link">
                            <p class="body-1">What are you looking for:</p>
                            <a href="#" class="current body-1">Apartment,</a>
                            <a href="#" class="body-1">Villa,</a>
                            <a href="#" class="body-1">Studio,</a>
                            <a href="#" class="body-1">House</a>
                        </div>

                    </div>
                </div>


            </div>

        </div>
        <div class="img-banner-left">
            <img src="{{ asset('frontend/images/slider/graplic-slider-2.png') }}" alt="img">
        </div>
        <div class="img-banner-right">
            <div class="swiper slider-sw-home2">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="slider-home2 img-animation wow">
                            <img src="{{ asset('frontend/images/slider/slider-2.jpg') }}" alt="images">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slider-home2">
                            <img src="{{ asset('frontend/images/slider/slider-2-1.jpg') }}" alt="images">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slider-home2">
                            <img src="{{ asset('frontend/images/slider/slider-2-2.jpg') }}" alt="images">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slider-home2">
                            <img src="{{ asset('frontend/images/slider/slider-2-3.jpg') }}" alt="images">
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>
    <!-- End Slider -->
    <!-- Latest Project-->
         @if($latestProject->count() > 0)
            <section class="flat-section flat-recommended pt-0 wow fadeInUpSmall mt-5">
                <div class="container">
                    <div class="box-title style-2 text-center">
                        {{-- <div class="text-subtitle text-primary">Carefully Chosen Listings</div> --}}
                        <h2 class="title mt-4">Latest Projects Launches</h2>
                    </div>
                    <div class="row">
                        @foreach ($latestProject as $p)
                            @php   
                                    $defaultImage = 'assets/images/defalt/dummy_property_images.png';

                                    if (!empty($p->media_url)  ) {
                                        $imagePath =  $p->media_url;
                                    } else {
                                        $imagePath = asset($defaultImage);
                                    } 
                            @endphp

                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="homeya-box">
                                    <div class="archive-top">

                                        {{-- IMAGE --}}
                                        <a href="{{ route('project-details', $p->slug) }}" class="images-group">
                                            <div class="images-style">
                                                <img src="{{$imagePath}}"
                                                    alt="img">
                                            </div>
                                            <div class="top">
                                                <ul class="d-flex gap-8">
                                                    <li class="flag-tag success">Featured</li>
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

                                            @if(isset($p->city) && !empty($p->city))
                                                <div class="desc">
                                                    <i class="fs-16 icon icon-mapPin"></i>
                                                    <p>{{ $p->city }}</p>
                                                </div>
                                            @else
                                                <div class="desc" style="visibility:hidden;">
                                                    <i class="fs-16 icon icon-mapPin"></i>
                                                    <p>Location</p>
                                                </div>
                                            @endif
                                            <ul class="meta-list">
                                                <li class="item">
                                                    <i class="icon icon-bed"></i>
                                                    <span> 
                                                        @if($p->bedrooms)
                                                            {{ $p->bedrooms }} 
                                                        @endif
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center">
                        <a href="{{ route('project') }}" class="tf-btn primary size-1">View All Projects</a>
                    </div>
                </div>
            </section>
        @endif

    <!----End Latest Project-->
    <!-- Categories -->
    <section class="flat-section flat-categories">
        <div class="container">
            <div class="box-title style-1 wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                <div class="box-left">
                    <div class="text-subtitle text-primary">EXPLORE BY TYPE</div>
                    <h2 class="title mt-4">Find the Space That Fits</h2>
                </div>
                <a href="#" class="btn-view"><span class="text">View All Services</span> <span class="icon icon-arrow-right2"></span> </a>
            </div>
            <div class="wrap-categories  wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                <div class="swiper tf-sw-categories" data-preview-lg="6" data-preview-md="4" data-preview-sm="3"
                    data-space="30">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <a href="{{ route('properties', ['type' => 'apartment']) }}" class="homeya-categories">
                                <div class="icon-box">
                                    <span class="icon icon-apartment"></span>
                                </div>
                                <div class="content text-center">
                                    <h6>Apartment</h6>
                                    {{-- <p class="mt-4 text-variant-1">234 Property</p> --}}
                                </div>
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a href="{{ route('properties', ['type' => 'villa']) }}" class="homeya-categories">
                                <div class="icon-box">
                                    <span class="icon icon-villa"></span>
                                </div>
                                <div class="content text-center">
                                    <h6>Villa</h6>
                                    {{-- <p class="mt-4 text-variant-1">234 Property</p> --}}
                                </div>
                            </a>

                        </div>
                        <div class="swiper-slide">
                            <a href="{{ route('properties', ['type' => 'studio']) }}" class="homeya-categories active">
                                <div class="icon-box">
                                    <span class="icon icon-studio"></span>
                                </div>
                                <div class="content text-center">
                                    <h6>Studio</h6>
                                    {{-- <p class="mt-4 text-variant-1">234 Property</p> --}}
                                </div>
                            </a>

                        </div>
                        <div class="swiper-slide">
                            <a href="{{ route('properties', ['type' => 'office']) }}" class="homeya-categories">
                                <div class="icon-box">
                                    <p class="icon icon-office"></p>
                                </div>
                                <div class="content text-center">
                                    <h6>Office</h6>
                                    {{-- <p class="mt-4 text-variant-1">234 Property</p> --}}
                                </div>
                            </a>

                        </div>
                        <div class="swiper-slide">
                            <a href="{{ route('properties', ['type' => 'townhouse']) }}" class="homeya-categories">
                                <div class="icon-box">
                                    <p class="icon icon-townhouse"></p>
                                </div>
                                <div class="content text-center">
                                    <h6>Townhouse</h6>
                                    {{-- <p class="mt-4 text-variant-1">234 Property</p> --}}
                                </div>
                            </a>

                        </div>
                        <div class="swiper-slide">
                            <a href="{{ route('properties', ['type' => 'commercial']) }}" class="homeya-categories">
                                <div class="icon-box">
                                    <span class="icon icon-commercial"></span>
                                </div>
                                <div class="content text-center">
                                    <h6>Commercial</h6>
                                    {{-- <p class="mt-4 text-variant-1">234 Property</p> --}}
                                </div>
                            </a>

                        </div>
                        <div class="swiper-slide">
                            <a href="{{ route('properties', ['type' => 'apartment']) }}" class="homeya-categories">
                                <div class="icon-box">
                                    <span class="icon icon-apartment"></span>
                                </div>
                                <div class="content text-center">
                                    <h6>Apartment</h6>
                                    {{-- <p class="mt-4 text-variant-1">234  Property</p> --}}
                                </div>
                            </a>
                        </div>
                    </div>

                </div>
                <div class="box-navigation">
                    <div class="navigation style-1 swiper-nav-next nav-next-category"><span
                            class="icon icon-arr-l"></span></div>
                    <div class="navigation style-1 swiper-nav-prev nav-prev-category"><span class="icon icon-arr-r"></span></div>
                </div>

            </div>
        </div>
    </section>
    <!-- End Categories -->
    <!-- Recommended -->
    @if($units->count() > 0)
    <section class="flat-section flat-recommended pt-0 wow fadeInUpSmall">
        <div class="container">
            <div class="box-title style-2 text-center">
                <div class="text-subtitle text-primary">Carefully Chosen Listings</div>
                <h2 class="title mt-4">Curated. Clear. Trusted.</h2>
            </div>
            <div class="row">
                @foreach ($units as $unit)
                    @php
                        $image = !empty($unit->images) ? $unit->images[0] : 'assets/images/defalt/dummy_property_images.png';
                    @endphp 
                    @php
                        $defaultImage = 'assets/images/defalt/dummy_property_images.png';

                        if (!empty($unit->images) && is_array($unit->images) && isset($unit->images[0])) {
                            $imagePath = asset('storage/' . $unit->images[0]);
                        } else {
                            $imagePath = asset($defaultImage);
                        }
                    @endphp

                    <div class="col-xl-3 col-md-6">
                        <div class="homeya-box md">
                            <div class="archive-top">
                                <a href="{{ $unit->slug ? route('property.details', $unit->slug) : '#' }}" class="images-group">
                                    <div class="images-style">
                                        <img src="{{$imagePath }}" alt="{{ $unit->property_name }}" style="height:230px;">
                                        {{-- <img src="{{ asset('storage/' . $image) }}" alt="{{ $unit->property_name }}"> --}}
                                    </div>

                                    <div class="top">
                                        <ul class="d-flex gap-8">
                                            @if ($unit->is_featured)
                                                <li class="flag-tag success">Featured</li>
                                            @endif

                                            @if ($unit->is_for_sale)
                                                {{-- <li class="flag-tag style-1">For Sale</li> --}}
                                            @endif
                                        </ul>
                                        <ul class="d-flex gap-4">
                                            <li class="box-icon w-32">
                                                <span class="icon icon-arrLeftRight"></span>
                                            </li>
                                            <li class="box-icon w-32">
                                                <span class="icon icon-heart"></span>
                                            </li>
                                            <li class="box-icon w-32">
                                                <span class="icon icon-eye"></span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="bottom">
                                        <span class="flag-tag style-2">
                                            {{ $unit->unitType->name ?? 'Unit' }}
                                        </span>
                                    </div>
                                </a>

                                <div class="content">
                                    <div class="text-1 text-capitalize">
                                        <a href="{{ $unit->slug ? route('property.details', $unit->slug) : '#' }}" class="link property-text" >
                                            {{ $unit->property_name ?? 'Property' }}
                                        </a>
                                    </div>

                                    <div class="desc">
                                        <i class="fs-16 icon icon-mapPin"></i>
                                        <p>
                                            {{ $unit->street ?? '' }},
                                            {{ $unit->city_name ?? '' }},
                                            {{ $unit->country->country_name ?? '' }}
                                        </p>
                                    </div>

                                    <ul class="meta-list">
                                        <li class="item">
                                            <i class="icon icon-bed"></i>
                                            <span>{{ $unit->bedrooms ?? '-' }}</span>
                                        </li>
                                        <li class="item">
                                            <i class="icon icon-bathtub"></i>
                                            <span>{{ $unit->no_of_bathrooms ?? '-' }}</span>
                                        </li>
                                        <li class="item">
                                            <i class="icon icon-ruler"></i>
                                            <span>{{ $unit->size ?? '-' }} SqFt</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="archive-bottom d-flex justify-content-between align-items-center">
                                <p>{{ $unit->developer->name ?? 'Developer' }}</p>

                                <div class="d-flex align-items-center">
                                    <div class="h7 fw-7">
                                         @php
                                                $countryCode = session('country_code');

                                                if ($countryCode === 'in') {
                                                    $currency = '₹'; 
                                                } else {
                                                    $currency = 'AED';
                                                   
                                                }
                                            @endphp
                                        {{ $currency }} {{ priceShort($unit->selling_price) }}
                                        {{-- {{ $unit->formatted_price }} --}}
                                    </div>
                                    @if (!$unit->is_for_sale)
                                        <p class="text-variant-1">/month</p>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center">
                <a href="{{ route('properties_search') }}" class="tf-btn primary size-1">View All Properties</a>
            </div>
        </div>
    </section>
    @endif

    <!-- End Recommended -->
    <!-- Service -->
    <section class="flat-section-v3 flat-service-v2 bg-surface">
        <div class="container">
            <div class="row wrap-service-v2">
                <div class="col-lg-6">
                    <div class="box-left wow fadeInLeftSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                        <div class="box-title">
                            <div class="text-subtitle text-primary">The Vserve Difference</div>
                            <h3 class="mt-1">Clear guidance at every step.</h3>
                            {{-- <h1 class="mt-4">Discover What Sets Our Real Estate Expertise Apart</h1> --}}
                        </div>
                        <p>We don’t start with listings — we start with listening.
                            By understanding your requirements, timeline & priorities, we help you navigate options that actually make sense. Backed by strong market insight & a transparent process, we stay involved from the first conversation to the final decision, keeping everything clear & aligned with your goals.
                        </p>
                        <ul class="list-view">

                                <li>
                                    <div class="item-wrapper">
                                        <svg class="check-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M8 15.9947C12.4183 15.9947 16 12.4154 16 8C16 3.58462 12.4183 0.00524902 8 0.00524902C3.58172 0.00524902 0 3.58462 0 8C0 12.4154 3.58172 15.9947 8 15.9947Z" fill="#198754"/>
                                            <path d="M7.35849 12.2525L3.57599 9.30575L4.65149 7.9255L6.97424 9.735L10.8077 4.20325L12.2462 5.19975L7.35849 12.2525Z" fill="white"/>
                                        </svg>
                                        <div class="content">
                                            <strong class="list-title">Transparent Process</strong>
                                            <p class="list-desc">No hidden steps. No unclear advice.</p>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="item-wrapper">
                                        <svg class="check-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M8 15.9947C12.4183 15.9947 16 12.4154 16 8C16 3.58462 12.4183 0.00524902 8 0.00524902C3.58172 0.00524902 0 3.58462 0 8C0 12.4154 3.58172 15.9947 8 15.9947Z" fill="#198754"/>
                                            <path d="M7.35849 12.2525L3.57599 9.30575L4.65149 7.9255L6.97424 9.735L10.8077 4.20325L12.2462 5.19975L7.35849 12.2525Z" fill="white"/>
                                        </svg>
                                        <div class="content">
                                            <strong class="list-title">Proven Market Insight</strong>
                                            <p class="list-desc">Decisions shaped by real market understanding.</p>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="item-wrapper">
                                        <svg class="check-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M8 15.9947C12.4183 15.9947 16 12.4154 16 8C16 3.58462 12.4183 0.00524902 8 0.00524902C3.58172 0.00524902 0 3.58462 0 8C0 12.4154 3.58172 15.9947 8 15.9947Z" fill="#198754"/>
                                            <path d="M7.35849 12.2525L3.57599 9.30575L4.65149 7.9255L6.97424 9.735L10.8077 4.20325L12.2462 5.19975L7.35849 12.2525Z" fill="white"/>
                                        </svg>
                                        <div class="content">
                                            <strong class="list-title">Tailored Recommendations</strong>
                                            <p class="list-desc">Options chosen to match your needs, not a template.</p>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="item-wrapper">
                                        <svg class="check-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M8 15.9947C12.4183 15.9947 16 12.4154 16 8C16 3.58462 12.4183 0.00524902 8 0.00524902C3.58172 0.00524902 0 3.58462 0 8C0 12.4154 3.58172 15.9947 8 15.9947Z" fill="#198754"/>
                                            <path d="M7.35849 12.2525L3.57599 9.30575L4.65149 7.9255L6.97424 9.735L10.8077 4.20325L12.2462 5.19975L7.35849 12.2525Z" fill="white"/>
                                        </svg>
                                        <div class="content">
                                            <strong class="list-title">Local Perspective</strong>
                                            <p class="list-desc">On-ground knowledge that adds real context.</p>
                                        </div>
                                    </div>
                                </li>

                            </ul>

                        <a href="{{ route('contact-us') }}" class="btn-view"><span class="text">Contact Us</span> <span
                                class="icon icon-arrow-right2"></span> </a>
                    </div>

                </div>
                <div class="col-lg-6">
                    <div class="box-right wow fadeInRightSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                        <div class="box-service style-1 hover-btn-view">
                            <div class="icon-box">
                                <span class="icon icon-buy-home"></span>
                            </div>
                            <div class="content">
                                <h6 class="title">Buy a Space</h6>
                                <p class="description">Browse verified options & get clear guidance to help you buy with confidence.</p>
                                <a href="#" class="btn-view style-1"><span class="text">Learn More</span> <span
                                        class="icon icon-arrow-right2"></span> </a>
                            </div>
                        </div>
                        <div class="box-service style-1 hover-btn-view">
                            <div class="icon-box">
                                <span class="icon icon-rent-home"></span>
                            </div>
                            <div class="content">
                                <h6 class="title">Rent a Space</h6>
                                <p class="description">Explore available spaces that fit your needs, location & budget.</p>
                                <a href="#" class="btn-view style-1"><span class="text">Learn More</span> <span
                                        class="icon icon-arrow-right2"></span> </a>
                            </div>
                        </div>
                        <div class="box-service style-1 hover-btn-view">
                            <div class="icon-box">
                                <span class="icon icon-sale-home"></span>
                            </div>
                            <div class="content">
                                <h6 class="title">Sell a Space</h6>
                                <p class="description">Market your space effectively & connect with serious buyers faster.</p>
                                <a href="#" class="btn-view style-1"><span class="text">Learn More</span> <span
                                        class="icon icon-arrow-right2"></span> </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
     @if($agent->count() > 0) 
    <section class="flat-section flat-agents">
        <div class="container">
            <div class="box-title text-center wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                <div class="text-subtitle text-primary">The Team Behind Vserve</div>
                <h2 class="title mt-4">Meet the Experts</h2>
            </div>
            <div class="row wow fadeInUpSmall" data-wow-delay=".4s" data-wow-duration="2000ms">
                @foreach($agent as $agent)
                <div class="box col-lg-4 col-sm-6">
                    <div class="box-agent style-1 hover-img"> 
                        <a href="#" class="box-img img-style agent-img">
                            <img src="{{ asset('storage/'.$agent->image) }}" alt="image-agent">
                            <ul class="agent-social">
                                <li>
                                    <span class="icon icon-facebook"
                                        onclick="window.open('{{ $agent->facebook }}','_blank')"
                                        style="cursor:pointer;"></span>
                                </li>
                                <li>
                                    <span class="icon icon-linkedin"
                                        onclick="window.open('{{ $agent->linkedin }}','_blank')"
                                        style="cursor:pointer;"></span>
                                </li>
                                <li>
                                    <span class="icon icon-twitter"
                                        onclick="window.open('{{ $agent->twitter }}','_blank')"
                                        style="cursor:pointer;"></span>
                                </li>
                                <li>
                                    <span class="icon icon-instagram"
                                        onclick="window.open('{{ $agent->instagram }}','_blank')"
                                        style="cursor:pointer;"></span>
                                </li>
                            </ul>
                        </a>
                        <a href="#" class="content">
                            <div class="info">
                                <h6 class="link"> {{ ucwords($agent->name)}}</h6>
                                <p class="mt-4 text-variant-1">{{ ucwords($agent->desgination)}}</p>
                            </div>
                            <span class="icon-phone"></span>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flat-bt-top text-center mt-5">
                <a class="tf-btn primary" href="{{route('all_agents')}}">View All Agents</a>
            </div>
        </div>
    </section>
    @endif
    <!-- End Agents -->

    <!-- Testimonial -->
   @if($testimonial && $testimonial->count() > 0)
    <section class="flat-section-v2 flat-testimonial-v2 wow fadeInUpSmall"
        data-wow-delay=".2s"
        data-wow-duration="2000ms">

        <div class="container">

            <div class="box-title text-center position-relative">
                <div class="text-subtitle text-white">Our Testimonials</div>
                <h2 class="title mt-4 text-white">What’s people say’s</h2>
            </div>

            <div class="swiper tf-sw-testimonial"
                data-preview-lg="3"
                data-preview-md="2"
                data-preview-sm="1"
                data-space="30">

                <div class="swiper-wrapper">

                    @foreach($testimonial as $item)
                    <div class="swiper-slide">
                        <div class="box-tes-item style-1">

                            {{-- ⭐ Dynamic Rating --}}
                            <ul class="list-star">
                                @php
                                    $rating = $item->rating_number ?? 5;
                                @endphp

                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $rating)
                                        <li class="icon icon-star text-warning"></li>
                                    @else
                                        <li class="icon icon-star text-muted"></li>
                                    @endif
                                @endfor
                            </ul> 
                            <p class="note body-1 testimonial-text">
                                {{ $item->description ?? 'No description available.' }}
                            </p>

                            <div class="box-avt d-flex align-items-center gap-12"> 
                                <div class="avatar avt-60 round">
                                    <img
                                        src="{{ 
                                            (!empty($item->profile) && file_exists(public_path('storage/'.$item->profile))) 
                                            ? asset('storage/'.$item->profile) 
                                            : asset('assets/images/defalt/dummy_property_images.png') 
                                        }}"
                                        alt="avatar"
                                    >
                                </div>

                                <div class="info"> 
                                    <div class="h7 fw-7">
                                        {{ !empty($item->name) ? ucwords($item->name) : 'Admin' }}
                                    </div> 
                                    @if(!empty($item->designation))
                                        <p class="text-variant-1 mt-4">
                                            {{ ucwords($item->designation) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>

                <div class="sw-pagination sw-pagination-testimonial"></div>

            </div>
        </div>
    </section>
    @endif

    <!-- End Testimonial -->
    <!-- Latest New -->
    @if($blogs && $blogs->count() > 0)
    <section class="flat-section flat-latest-new wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
        <div class="container">
            <div class="box-title text-center">
                <div class="text-subtitle text-primary">Latest News</div>
                <h2 class="title mt-4">Helpful Homeya Guides</h2>
            </div>
            <div class="row">
                @foreach ($blogs as $blog)
                    <div class="box col-lg-4 col-md-6">
                    <a href="{{route('news-details', $blog->slug)}}" class="flat-blog-item hover-img">
                        <div class="img-style blog-img-box">
    @php
        $defaultImage = 'assets/images/defalt/dummy_property_images.png';

        if (!empty($blog->featured_image) && Storage::disk('public')->exists($blog->featured_image)) {
            $Newsimg = asset('storage/'.$blog->featured_image);
        } else {
            $Newsimg = asset($defaultImage);
        }
    @endphp

    <img src="{{ $Newsimg }}" alt="img-blog">

    <span class="date-post">
        {{ $blog->created_at->format('F d, Y') }}
    </span>
</div><div class="content-box">
                            <div class="post-author">
                                
                              <span>{{ ucwords($blog->category ?? '') ?: "\u{00A0}" }}</span>
                            </div>
                            <h6 class="title two-line">{{ ucwords($blog->title)}}</h6>
                            <p class="description two-line">{{$blog->short_description}}</p>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @else
    <br>
    @endif
    <!-- End Latest New -->
@endsection
@section('scripts')
<script>
$(document).ready(function () {

    /* UNIT TYPE */
    $(document).on('click', '.js-unit-type .option', function () {
        $('#filter_unit_type').val($(this).data('value'));
        $('.js-unit-type .current').text($(this).text());
    });

    /* AMENITIES */
    $('.js-amenity').on('change', function () {
        let values = [];
        $('.js-amenity:checked').each(function () {
            values.push($(this).val());
        });
        $('#filter_amenities').val(values.join(','));
    });


       let activePurpose = $('.nav-link-item.active').data('purpose');
        $('#property_purpose').val(activePurpose); 
        $('.nav-link-item').on('click', function(){
            let purpose = $(this).data('purpose');
            $('#property_purpose').val(purpose);
        });



});
</script>

@endsection
