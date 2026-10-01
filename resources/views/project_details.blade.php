@extends('layouts.frontend.frontend_layout')
@section('main-content')
    <style>
        #shareModal {
    z-index: 99999 !important;
}

#shareModal .modal-dialog {
    z-index: 999999 !important;
    position: relative;
}

#shareModal .modal-content {
    position: relative;
    z-index: 999999 !important;
    pointer-events: auto !important;
}

#shareModal .modal-body,
#shareModal .share-grid,
#shareModal .share-card {
    position: relative;
    z-index: 999999 !important;
    pointer-events: auto !important;
}

#shareModal .share-card {
    cursor: pointer;
}

#shareModal .share-card i,
#shareModal .share-card span {
    pointer-events: none;
}

        .amenity-ic {
            width: 50px;
            height: 50px;
            object-fit: contain;
            margin-right: 10px;
            border-radius: 50%;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .flat-slider-detail-v1 .box-imgage-detail {
            width: 100%;
            height: 420px;
            overflow: hidden;
            border-radius: 12px;
        }

        .flat-slider-detail-v1 .box-imgage-detail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
.share-modal {
    border-radius: 16px;
    overflow: hidden;
}
.share-icons {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}
.share-btn {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px 10px;
    text-align: center;
    text-decoration: none;
    color: #111827;
    background: #fff;
    transition: 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}
.share-btn i {
    font-size: 22px;
}
.share-btn span {
    font-size: 13px;
    font-weight: 600;
}
.share-btn:hover {
    background: #f9fafb;
    color: #111827;
}
.copy-link {
    width: 100%;
    border: 1px solid #e5e7eb;
}
@media (max-width: 575px) {
    .share-icons {
        grid-template-columns: repeat(2, 1fr);
    }
}

        .bg-soft-success {
            background-color: #d1e7dd;
        }

        .bg-soft-primary {
            background-color: #e7f1ff;
        }

        .text-primary {
            color: #002147 !important;
        }

        .list-group-item {
            border-color: #f1f1f1 !important;
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }

        @media (max-width: 768px) {
            .border-end {
                border-right: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }
        }
        b {
            font-weight: bold !important;
        }
    </style>

    @php
        $defaultImages = [
            asset('frontend/images/banner/banner-property-1.jpg'),
            asset('frontend/images/banner/banner-property-3.jpg'),
            asset('frontend/images/banner/banner-property-2.jpg'),
        ];

        $mediaCount = $media->count();

        if ($mediaCount === 0) {
            $sliderImages = $defaultImages;
        } elseif ($mediaCount === 1) {
            $single = $media->first()->url; // your S3 url
            $sliderImages = [$single, $single, $single];
        } else {
            $sliderImages = $media->pluck('url')->toArray();
        }
    @endphp

    <section class="flat-location flat-slider-detail-v1">
        <div class="swiper tf-sw-location" data-preview-lg="2.03" data-preview-md="2" data-preview-sm="2" data-space="20"
            data-centered="true" data-loop="true">

            <div class="swiper-wrapper">
                @foreach ($sliderImages as $img)
                    <div class="swiper-slide">
                        <a href="{{ $img }}" data-fancybox="gallery" class="box-imgage-detail d-block">
                            <img src="{{ $img }}" alt="img-property">
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="box-navigation">
                <div class="navigation swiper-nav-next nav-next-location"><span class="icon icon-arr-l"></span></div>
                <div class="navigation swiper-nav-prev nav-prev-location"><span class="icon icon-arr-r"></span></div>
            </div>
        </div>
    </section>

    <section class="flat-section pt-0 flat-property-detail">
        <div class="container">
            <div class="header-property-detail">
                <div class="content-top d-flex justify-content-between align-items-center">
                    <div class="box-name">

                        <h1 class="title link">{{ $data->name }}</h1>

                    </div>
                    @php
                        /* $brochureUrl = $project_media->url ?? null;
                        $path = $brochureUrl ? explode('storage/', $brochureUrl)[1] ?? null : null; */
                        $brochureUrl = $project_brochure->url ?? null;
                        $path = $brochureUrl ? explode('storage/', $brochureUrl)[1] ?? null : null;
                        $hasBrochure = $path && Storage::disk('public')->exists($path);
                    @endphp

                    {{-- @if ($hasBrochure) --}}
                        <div class="box-price d-flex align-items-center">
                            <div class="flat-bt-top">
                                {{-- <a class="tf-btn primary" href="{{ $brochureUrl }}" target="_blank">
                                    Download Brochure
                                </a> --}}
                                <a href="javascript:void(0)"
                                    class="tf-btn primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#brochureModal">
                                        Download Brochure
                                </a>
                            </div>
                        </div>
                    {{-- @endif --}}
                </div>
                <div class="content-bottom">
                    {{-- <div class="info-box">
                        <div class="label">FEATURES:</div>
                        <ul class="meta">
                            <li class="meta-item"><span class="icon icon-bed"></span> 2 Bedroom</li>
                            <li class="meta-item"><span class="icon icon-bathtub"></span> 2 Bathroom</li>
                            <li class="meta-item"><span class="icon icon-ruler"></span> 6,000 SqFT</li>
                        </ul>
                    </div> --}}
                    <div class="info-box">
                        <div class="label">Starting Price:</div>
                        <ul class="meta">
                            <h4>{{ $data->price_currency ?? '' }}
                                {{ priceShort($data->min_price ?? null) }} </h4>
                        </ul>
                    </div>
                    <div class="info-box">
                        @if($data->locality || $data->city)
<div>
    <div class="label">LOCATION:</div>
    <p class="meta-item">
        <span class="icon icon-mapPin"></span>
        {{ $data->locality }}{{ $data->locality && $data->city ? ', ' : '' }}{{ $data->city }}
    </p>
</div>
@endif


                </div>
            </div>
       @php
    $countryCode = strtolower(session('country_code') ?? '');

    $projectName = $data->name ?? 'This project';
    $projectType = $data->type ?? $data->property_type ?? 'premium';
    $projectLocation = $data->locality ?? $data->location ?? 'a prime location';
    $projectCity = $data->city ?? '';
    $developerName = $data->developer_name ?? ($data->developer->name ?? 'a reputed developer');

    $unitTypes = collect($plans ?? [])
        ->map(function ($plan) {
            if (($plan->bedrooms ?? null) === 0) {
                return 'Studio';
            }
            return !empty($plan->bedrooms) ? $plan->bedrooms .'  '.$plan->room_type : null;
        })
        ->filter()
        ->unique()
        ->values()
        ->implode(', ');

    $unitTypes = $unitTypes ;
    // $unitTypes = $unitTypes ?: 'homes';

    $propertyTypes = collect($plans ?? [])
        ->map(function ($plan) {
            if (($plan->bedrooms ?? null) === 0) {
                return 'Studio';
            }
            return !empty($plan->bedrooms) ? $plan->bedrooms . ' Bedroom' : null;
        })
        ->filter()
        ->unique()
        ->values()
        ->implode(', ');

    $propertyTypes = $propertyTypes ;
    // $propertyTypes = $propertyTypes ?: 'homes';

    $startingPrice = (!empty($data->price_currency) || !empty($data->min_price))
        ? trim(($data->price_currency ?? '') . ' ' . priceShort($data->min_price ?? null))
        : 'price on request';

    $amenitiesText = collect($amenities ?? [])
        ->pluck('name')
        ->filter()
        ->take(8)
        ->implode(', ');

    $amenitiesText = $amenitiesText ;
    // $amenitiesText = $amenitiesText ?: 'modern lifestyle amenities';

    $handoverDate = $data->handover ?? $data->construction_end_date ?? 'to be announced';
    $reraNumber = $data->rera_no ?? 'coming soon';

   if ($countryCode === 'ae') {
    $dynamicDescription = "
    <p><b>{$projectName}</b> is a premium development by <b>{$developerName}</b> located in <b>{$projectLocation}" .
    ($projectCity ? ", {$projectCity}" : "") .
    "</b>. 
    " .
   
    (!empty($propertyTypes)
        ? "The project offers well-designed <b>{$propertyTypes}</b> with modern architecture and efficient layouts."
        : "The development is planned with essential infrastructure and well-designed spaces to support everyday convenience and functionality."
    ) . "
    
    
    

    With a starting price of <b>{$startingPrice}</b>, the development is designed to suit both investors and end users seeking quality real estate in Dubai.

   </p>". 
   (!empty($amenitiesText)
        ? " <p class='mt-2'> The project also features a range of lifestyle and convenience facilities, including <b>{$amenitiesText}</b>."
        : "<p class='mt-2'> The development is planned with essential infrastructure and well-designed spaces to support everyday convenience and functionality.</p></b>")

   ."
   
   
  

    Buyers can explore detailed project information, expected handover date <b>{$handoverDate}</b>, and download the <b>{$projectName} brochure</b> for complete specifications and development insights.
    </p>";
} else { 
    $dynamicDescription = "
    <p><b>{$projectName}</b> is a <b>{$projectType}</b> development located in <b>{$projectLocation}" .
    ($projectCity ? ", {$projectCity}" : "") .
    "</b>. " .
   
    (!empty($unitTypes)
        ? "The project offers well-planned <b>{$unitTypes}</b> designed with efficient layouts and modern infrastructure."
        : "The project features thoughtfully planned commercial spaces designed with efficient layouts and modern infrastructure to support business needs."
    ) . "

    With a starting price of <b>{$startingPrice}</b>, the development is planned to meet the evolving needs of buyers and investors while ensuring convenience and long-term value.</p>
    ".
        (!empty($amenitiesText)
            ? " <p class='mt-2'>A range of facilities is available within the project, including <b>{$amenitiesText}</b>."
            : "<p class='mt-2'>The project features thoughtfully planned commercial spaces designed with efficient layouts and modern infrastructure to support business needs.</p></b>"
        )
    ."
    

    The project is registered under RERA number <b>{$reraNumber}</b>, ensuring transparency for buyers.

    Interested buyers can also download the <b>{$projectName} brochure</b> to explore complete project specifications, layout details, and other important information.
    </p>";
}
@endphp


            <div class="row">
                <div class="col-lg-8">
                        <div class="single-property-element single-property-desc">
                            <div class="h7 title fw-7">Description</div>
                     <div>
                        @if(trim(strip_tags($data->building_description)) != "")
    {!! $data->building_description !!}
@else
    {!! $dynamicDescription !!}
@endif
                    </div>
                        </div>

                    <div class="single-property-element mb-5">
                        <div class="h7 title fw-7 mb-4 px-2">Project Overview & Specifications</div>

                        <div class="bg-white rounded-4 shadow-sm border overflow-hidden">
                            <div class="row g-0">

                                <div class="col-md-6 border-end">
                                    <ul class="list-group list-group-flush">
                                        @if (!empty($data->status))
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-bottom">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-info-square text-primary me-3 fs-5"></i>
                                                    <span class="text-muted">Status</span>
                                                </div>
                                                <span
                                                    class="fw-bold badge bg-soft-success text-success rounded-pill px-3">{{ ucwords(str_replace('_', ' ', $data->status)) }}</span>
                                            </li>
                                        @endif
                                        @if (!empty($data->units_count))
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-bottom">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-stack text-primary me-3 fs-5"></i>
                                                    <span class="text-muted">Total Units</span>
                                                </div>
                                                <span class="fw-bold">{{ $data->units_count }} Units</span>
                                            </li>
                                        @endif

                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-patch-check text-primary me-3 fs-5"></i>
                                                <span class="text-muted">RERA Number</span>
                                            </div>
                                            <span class="fw-bold">{{ $data->rera_no ?? 'Coming Soon' }}</span>
                                        </li>
                                        @if (!empty($data->launch_date))
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-bottom">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-rocket-takeoff text-primary me-3 fs-5"></i>
                                                    <span class="text-muted">Launch Date</span>
                                                </div>
                                                <span class="fw-bold">{{ $data->launch_date }}</span>
                                            </li>
                                        @endif
                                        @if (!empty($data->furnishing))
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-bottom">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-lamp text-primary me-3 fs-5"></i>
                                                    <span class="text-muted">Furnishing</span>
                                                </div>
                                                <span
                                                    class="fw-bold">{{ ucwords(str_replace('_', ' ', $data->furnishing)) }}</span>
                                            </li>
                                        @endif
                                        @if (!empty($data->service_charge))
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-0">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-wallet2 text-primary me-3 fs-5"></i>
                                                    <span class="text-muted">Service Charge</span>
                                                </div>
                                                <span class="fw-bold">{{ $data->service_charge }}</span>
                                            </li>
                                        @endif
                                        @if ($data->handover)
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-0">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-wallet2 text-primary me-3 fs-5"></i>
                                                    <span class="text-muted">Handover</span>
                                                </div>
                                                <span class="fw-bold">{{ $data->handover }}</span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="col-md-6 bg-light bg-opacity-50 p-4">
                                    <div class="mb-4">
                                        <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Price
                                            Range</label>
                                        <div class="p-3 bg-white rounded-3 border-start border-primary border-4 shadow-sm">
                                            @if ($data->min_price && $data->max_price)
                                                <h4 class="fw-bold mb-1 text-primary">
                                                    {{ $data->price_currency ?? '' }}
                                                    @if ($data->min_price == $data->max_price)
                                                        {{ priceShort($data->min_price) }}
                                                    @else
                                                        {{ priceShort($data->min_price) }} -
                                                        {{ priceShort($data->max_price) }}
                                                    @endif
                                                </h4>
                                            @endif

                                            <div>
                                                @if ($data->location)
                                                    <small class="text-muted d-block">Location Strategy</small>
                                                    <span class="fw-bold">{{ $data->location }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Project
                                            Timeline</label>
                                        <div class="p-3 bg-white rounded-3 border-start border-success border-4 shadow-sm">
                                            <div class="d-flex justify-content-between align-items-center">
                                                @if ($data->construction_start_date)
                                                    <div>
                                                        <small class="d-block text-muted">Construction Start</small>
                                                        <span class="fw-bold">{{ $data->construction_start_date }}</span>
                                                    </div>
                                                @endif
                                                @if ($data->construction_end_date)
                                                    <i class="bi bi-arrow-right text-muted"></i>
                                                    <div class="text-end">
                                                        <small class="d-block text-muted">Handover Date</small>
                                                        <span
                                                            class="fw-bold text-success">{{ $data->construction_end_date }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="single-property-element single-property-feature">
                        <div class="h7 title fw-7">Amenities and features</div>

                        <div class="wrap-feature" style="display: inline !important;">
                            <div class="row">
                                @foreach ($amenities->chunk(ceil($amenities->count() / 2)) as $chunk)
                                    <div class="col-lg-4 col-md-12">
                                        <div class="box-feature">
                                            <ul>
                                                @foreach ($chunk as $a)
                                                    <li class="feature-item">
                                                        {{-- Icon (agar tum check icon hi chahte ho to img hata do) --}}
                                                        @if (!empty($a->icon_url))
                                                            <img src="{{ $a->icon_url }}" alt=""
                                                                class="amenity-ic">
                                                        @else
                                                            <span class="icon icon-checkCircle"></span>
                                                        @endif

                                                        {{ $a->name }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if (!empty($plans) && count($plans))
                        <div class="single-property-element mb-5">
                            <div style="font-family: sans-serif; max-width: 600px; ">
                                <h2 style="color: #003366; font-size: 32px; margin-bottom: 20px;">Floorplans</h2>
                                @foreach ($plans as $plan)
                                    <div style="border-top: 1px solid #e0e0e0; padding: 20px 0; font-size:23px; ">
                                        <div
                                            style="
                                                font-size: 20px;
                                                font-weight: bold;
                                                margin-bottom: 5px;
                                                color: #002b5c;
                                            ">
                                            @if ($plan->bedrooms == 0)
                                                Studio
                                            @else
                                                {{ $plan->bedrooms }}  
                                            @endif
                                            @if (!empty($plan->room_type))
                                                - {{ $plan->room_type }}
                                            @endif
                                        </div>

                                        @if ($plan->from_size_sqft)
                                            <div style=" font-size: 18px;">
                                                {{ rtrim(rtrim(number_format($plan->from_size_sqft, 2), '0'), '.') }}
                                                Sq.Ft.
                                            </div>
                                            <div style="margin-top:6px; font-size:16px; color:#111; font-weight:600;">
                                                <span style="font-size:14px; ">
                                                    {{ $plan->currency_code }}
                                                </span>
                                                <span>
                                                    {{ priceShort($plan->from_price, 2) }}
                                                </span>
                                            </div>
                                        @endif

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif


                    <div class="single-property-element single-property-map">
                        <div class="h7 title fw-7">Map</div>
                        {{-- <div id="map-single" class="map-single" data-lat="{{ $data->lat }}"
                            data-lng="{{ $data->lng }}" data-map-zoom="16" data-map-scroll="true"></div> --}}
                        <div class="map-single">
                            <iframe width="100%" height="400" style="border:0;border-radius:12px" loading="lazy"
                                allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps?q={{ $data->lat }},{{ $data->lng }}&z=16&output=embed">
                            </iframe>
                        </div>

                        <ul class="info-map">
                            <li>
                                <div class="fw-7">Address</div>
                                <span class="mt-4 text-variant-1">
                                    {{ $data->location ?? '—' }}
                                </span>
                            </li>
                        </ul>

                    </div>

                    <div class="single-property-element single-property-nearby">
                        <div class="h7 title fw-7">What’s nearby?</div>
                        <p class="body-2">Explore nearby amenities to precisely locate your property and identify
                            surrounding conveniences, providing a comprehensive overview of the living environment and the
                            property's convenience.</p>
                        @php
                            $points = collect($map_point);
                            $chunks = $points->chunk(ceil($points->count() / 2))->values();
                        @endphp

                        <div class="grid-2 box-nearby">
                            <ul class="box-left">
                                @foreach ($chunks->get(0, collect()) as $point)
                                    <li>
                                        <span class="label">{{ $point->name }}</span>
                                        <span class="fw-7" style="margin-left:10px;">
                                            {{ number_format($point->distance, 1) }} km ({{ $point->time }} min)
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            <ul class="box-right">
                                @foreach ($chunks->get(1, collect()) as $point)
                                    <li>
                                        <span class="label">{{ $point->name }}</span>
                                        <span class="fw-7" style="margin-left:10px;">
                                            {{ number_format($point->distance, 1) }} km ({{ $point->time }} min)
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @if (isset($plans_step) && $plans_step->count())
                        <div class="single-property-element mb-5">
                            <div class="h7 title fw-7 mb-2">Payment Plans</div>
                            <div class="row g-3">
                                @foreach ($plans_step as $plan)
                                    <div class="col-md-7">
                                        <div class="p-4 border rounded-3 shadow-sm d-flex align-items-center bg-white">
                                            <div class="display-5 fw-bold me-4" style="color: #002147; min-width: 120px;">
                                                {{ rtrim(rtrim($plan->percentage, '0'), '.') }}%
                                            </div>
                                            <div class="text-muted fs-5">
                                                {{ $plan->name }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif





                </div>

                <div class="col-lg-4">

                    <div class="widget-sidebar fixed-sidebar ">
                        <div class="widget-box single-property-contact bg-surface">
                            <div class="h7 title fw-7">Contact Sellers</div>
                           <div class="box-avatar">

    @php
        $countryCode = session('country_code');

        if ($countryCode === 'ae') {
            $image = asset('frontend/images/avatar/uae_contact.jpeg');
            $mb = '+971 58 586 2033';
        } else {
            $image = asset('frontend/images/avatar/ind_contact.jpeg');
            $mb = '+91-9211331048';
        }
    @endphp

    <div class="avatar avt-100 round">
        <img src="{{ $image }}" alt="avatar">
    </div>

    <div class="contact-number">
       <div class="text-1 name">Vserve Real Estate</div>
                                    <span>{{ $mb }} Info@vserverealestate.com</span>
    </div>

</div>



                            <form action="{{ route('contact.inquiries.store') }}" method="post" class="contact-form">
                                @csrf
                                <input type="hidden" name="p_id" value="{{ $data->id }}" />
                                <input type="hidden" name="type" value="project" />
                                <div class="ip-group">
                                    <label for="fullname">Full Name:</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name') }}" required
                                        oninput="this.value = this.value.replace(/[^A-z .]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                    <span class="text-danger">
                                        @error('name')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="ip-group">
                                    <label for="phone">Phone Number:</label>
                                    <input type="text"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                        maxlength="12" name="phone_no" class="form-control"
                                        value="{{ old('phone_no') }}" required>
                                    <span class="text-danger">
                                        @error('phone_no')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="ip-group">
                                    <label for="email">Email Address:</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email') }}" required>
                                    <span class="text-danger">
                                        @error('email')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="ip-group">
                                    <label for="message">Your Message:</label>
                                    <textarea name="message" rows="4" class="form-control" required>{{ old('message') }}</textarea>
                                    <span class="text-danger">
                                        @error('message')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <button class="tf-btn primary w-100">Send Message</button>
                                <span class="text-success d-block mt-2 text-center">
                                    @if (session('success'))
                                        We have received your inquiry. Our team will contact you shortly.
                                    @endif
                                </span>
                                <span class="text-danger d-block mt-2 text-center">
                                    @if (session('error'))
                                        Something went wrong. Please try again.
                                    @endif
                                </span>
                            </form>
                        </div>

                        <div class="widget-box single-property-whychoose bg-surface">
                            <div class="h7 title fw-7">Why Choose Us?</div>
                            <ul class="box-whychoose">
                                <li class="item-why">
                                    <i class="icon icon-secure"></i>
                                    Secure Booking
                                </li>
                                <li class="item-why">
                                    <i class="icon icon-guarantee"></i>
                                    Best Price Guarantee
                                </li>
                                <li class="item-why">
                                    <i class="icon icon-booking"></i>
                                    Easy Booking Process
                                </li>
                                <li class="item-why">
                                    <i class="icon icon-support"></i>
                                    Available Support 24/7
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="brochureModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content p-4" style="background:white; border-radius: 15px;">
                    <div class="modal-header border-0 pb-0">
                        <h4 class="modal-title fw-bold text-dark-blue" id="exampleModalLabel">Download Brochure</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-4">Please fill in your details below to download the brochure.</p>

                        <form id="brochureForm">
                            @csrf
                            <div class="row g-3 mb-4">
                                <div class="col-md-12 text-start">
                                    <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control form-control-lg" required
                                        placeholder="Enter your name">
                                </div>

                                <div class="col-md-6 text-start">
                                    <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control form-control-lg" required
                                        placeholder="Enter your email">
                                </div>

                                <div class="col-md-6 text-start">
                                    <label class="form-label fw-bold">Phone <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">+91</span>
                                        <input type="text" name="phone" class="form-control form-control-lg"
                                            maxlength="10" placeholder="10 digit number">
                                    </div>
                                    <small class="text-danger d-none" id="phoneError">
                                        Phone number is required & must be 10 digits
                                    </small>
                                </div>

                                {{-- <input type="hidden" name="project_media"
                                    value="{{ encrypt($project_media->url ?? '') }}"> --}}
                                <input type="hidden" name="project_media" value="{{ encrypt($project_brochure->url ?? '') }}">
                                <input type="hidden" name="project_id" value="{{ encrypt($data->id) }}">
                                <input type="hidden" name="has_brochure" value="{{ $hasBrochure ? 1 : 0 }}">
                            </div>

                            <button type="submit" class="btn tf-btn primary w-100 mt-4 btn-lg fw-bold py-3">
                                Download
                            </button>
                            <div id="formSuccess" class="alert alert-success mt-3 d-none text-center"></div>
                            <div id="formError" class="text-danger mt-3 d-none text-center"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </section>
@endsection
@section('scripts')
    <script>

</script>
    <script>
        $(document).ready(function() {
            function copyShareLink() {
    const input = document.getElementById('sharePageUrl');
    const msg = document.getElementById('copyMessage');

    input.select();
    input.setSelectionRange(0, 99999);

    navigator.clipboard.writeText(input.value).then(function () {
        msg.classList.remove('d-none');
        setTimeout(() => {
            msg.classList.add('d-none');
        }, 2000);
    });
}
            $('#brochureForm').on('submit', function(e) {
                e.preventDefault();

                let phone = $('input[name="phone"]').val().trim();
                let phoneError = $('#phoneError');
                let formError = $('#formError');
                let formSuccess = $('#formSuccess');
                let hasBrochure = $('input[name="has_brochure"]').val();

                formError.addClass('d-none').text('');
                formSuccess.addClass('d-none').text('');

                if (!/^[0-9]{10}$/.test(phone)) {
                    phoneError.removeClass('d-none');
                    return;
                } else {
                    phoneError.addClass('d-none');
                }

                $.ajax({
                    url: "{{ route('brochure.contact.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {

                        if (res.status === 'success') {
                            if (hasBrochure == 1 && res.download_url) {
                                window.location.href = res.download_url;

                                setTimeout(function() {
                                    $('#brochureModal').modal('hide');
                                    $('#brochureForm')[0].reset();
                                }, 500);
                            } else {
                                formSuccess
                                    .text('Brochure is not available right now. Our team will send it to your email shortly.')
                                    .removeClass('d-none');

                                setTimeout(function() {
                                    $('#brochureModal').modal('hide');
                                    $('#brochureForm')[0].reset();
                                    formSuccess.addClass('d-none').text('');
                                }, 2500);
                            }
                            /* window.location.href = res.download_url;

                            setTimeout(function() {
                                $('#brochureModal').modal('hide');
                                $('#brochureForm')[0].reset();
                            }, 500); */
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Something went wrong. Please try again.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }

                        formError.text(msg).removeClass('d-none');
                    }
                });
            });

        });
    </script>
@endsection
