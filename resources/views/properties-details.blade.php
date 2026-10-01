@extends('layouts.frontend.frontend_layout')
@section('main-content')
    <style>
        .map-single {
            height: 350px;
            width: 100%;
            border-radius: 12px;
        }
        .feature_height{
            height: 450px;
        }
    </style>

    {{-- <section class="flat-gallery-single">
                <div class="item1 box-img">
                    <img src="images/banner/banner-property-5.jpg" alt="img-gallery">
                    <div class="box-btn">
                        <a href="https://youtu.be/MLpWrANjFbI" data-fancybox="gallery2" class="box-icon">
                            <span class="icon icon-play2"></span>
                        </a>
                        <a href="images/banner/banner-property-9.jpg"  data-fancybox="gallery" class="tf-btn primary">View All Photos</a>
                    </div>
                </div>
                <a href="images/banner/banner-property-6.jpg" class="item2 box-img" data-fancybox="gallery">
                    <img src="images/banner/banner-property-6.jpg" alt="img-gallery">
                </a>
                <a href="images/banner/banner-property-7.jpg" class="item3 box-img" data-fancybox="gallery">
                    <img src="images/banner/banner-property-7.jpg" alt="img-gallery">
                </a>
                <a href="images/banner/banner-property-8.jpg" class="item4 box-img" data-fancybox="gallery">
                    <img src="images/banner/banner-property-8.jpg" alt="img-gallery">
                </a>
                <a href="images/banner/banner-property-9.jpg" class="item5 box-img" data-fancybox="gallery">
                    <img src="images/banner/banner-property-9.jpg" alt="img-gallery">
                </a>
            </section> --}}

    @php
        $images = $unit->images ?? [];
    @endphp



    @php
    $galleryImages = $unit->unitImages ?? collect();
    $mainImage = $galleryImages->first();
    $fallbackImage = asset('images/no-image.png');
@endphp

<section class="flat-gallery-single">
    <div class="item1 box-img">
        <img
            src="{{ $mainImage?->image_url ?: $fallbackImage }}"
            alt="img-gallery"
        >

        <div class="box-btn">
            @if ($unit->document_web)
                <a href="{{ $unit->document_web }}" data-fancybox="video-gallery" class="box-icon">
                    <span class="icon icon-play2"></span>
                </a>
            @endif

            @if ($galleryImages->count())
                <a href="{{ $mainImage->image_url }}"
                   data-fancybox="property-gallery"
                   class="tf-btn primary">
                    View All Photos
                </a>
            @endif
        </div>
    </div>

    @foreach ($galleryImages as $key => $image)
        @if ($key > 0 && $key <= 4)
            <a href="{{ $image->image_url }}"
               class="item{{ $key + 1 }} box-img"
               data-fancybox="property-gallery"
               data-thumb="{{ $image->image_url }}">
                <img src="{{ $image->image_url }}" alt="img-gallery">
            </a>
        @endif
    @endforeach

    @foreach ($galleryImages as $key => $image)
        @if ($key > 4)
            <a href="{{ $image->image_url }}"
               data-fancybox="property-gallery"
               data-thumb="{{ $image->image_url }}"
               style="display: none;"></a>
        @endif
    @endforeach
</section>

    <section class="flat-section-v6 flat-property-detail-v3">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="header-property-detail">
                        <div class="content-top d-flex justify-content-between align-items-center">
                            <div class="box-name">
                                <a href="#" class="flag-tag primary">
                                    {{ $unit->is_for_sale ? 'For Sale' : 'For Rent' }}
                                </a>
                                <h4 class="title link">{{ $unit->property_name }}</h4>
                            </div>
                            <div class="box-price d-flex align-items-center">
                                <h4>{{ $unit->currency_symbol }} 
                                    {{ priceShort($unit->selling_price ?? $unit->rent_price) }}
                                    {{-- {{ $unit->formatted_price }} --}}
                                </h4>
                                <span class="body-1 text-variant-1">
                                    {{ $unit->is_for_sale ? '' : '/month' }}
                                </span>
                            </div>
                        </div>
                        <div class="content-bottom">
                            <div class="info-box">
                                <div class="label">FEATUREs:</div>
                                <ul class="meta">
                                    <li class="meta-item">
                                        <span class="icon icon-bed"></span>
                                        {{ $unit->bedrooms }} Bedroom
                                    </li>
                                    <li class="meta-item">
                                        <span class="icon icon-bathtub"></span>
                                        {{ $unit->no_of_bathrooms }} Bathroom
                                    </li>
                                    <li class="meta-item">
                                        <span class="icon icon-ruler"></span>
                                        {{ $unit->builtup_area }} {{ $unit->area_measurement }}
                                    </li>
                                </ul>
                            </div>
                            
                        </div>
                    </div>

                    <div class="single-property-element single-property-desc">
                        <div class="h7 title fw-7">Description</div>
                        @php
                        
     $html = $unit->remarks ?? '';

    // Remove CDATA
    $html = str_replace(['<![CDATA[', ']]>'], '', $html);

    // Normalize breaks
    $html = str_replace(['<br />', '<br/>'], '<br>', $html);

    $lines = explode('<br>', $html);

    $formatted = '';
    $inList = false;

    foreach ($lines as $line) {
        $line = trim(html_entity_decode($line));

        if ($line === '') continue;

        // Bullet points
        if (str_starts_with($line, '-')) {
            if (!$inList) {
                $formatted .= '<ul>';
                $inList = true;
            }

            $formatted .= '<li>' . e(ltrim($line, '- ')) . '</li>';
        }

        // ✅ FIXED: Heading detection
        elseif (preg_match('/:\s*$/', $line)) {
            if ($inList) {
                $formatted .= '</ul>';
                $inList = false;
            }

            $formatted .= '<p><b>' . e($line) . '</b></p>';
        }

        // Normal text
        else {
            if ($inList) {
                $formatted .= '</ul>';
                $inList = false;
            }

            $formatted .= '<p>' . e($line) . '</p>';
        }
    }

    if ($inList) {
        $formatted .= '</ul>';
    }


    $overview = trim($unit->property_overview ?? '') ?: $formatted;
@endphp
                        <p class="body-2 text-variant-1">
                            {!! nl2br(($overview)) !!}
                        </p>
                    </div>

                    <div class="single-property-element single-property-overview">
                        <div class="h7 title fw-7">Overview</div>
                        <ul class="info-box">
                            <li class="item">
                                <a href="#" class="box-icon w-52"><i class="icon icon-house-line"></i></a>
                                <div class="content">
                                    <span class="label">ID:</span>
                                    <span>{{ $unit->code ?? $unit->unit_number }}</span>
                                </div>
                            </li>
                            <li class="item">
                                <a href="#" class="box-icon w-52"><i class="icon icon-arrLeftRight"></i></a>
                                <div class="content">
                                    <span class="label">Type:</span>
                                    <span>{{ $unit->category ?? optional($unit->unitType)->name }}</span>
                                </div>
                            </li>
                            <li class="item">
                                <a href="#" class="box-icon w-52"><i class="icon icon-bed"></i></a>
                                <div class="content">
                                    <span class="label">Bedrooms:</span>
                                    <span>{{ $unit->bedrooms }} Rooms</span>
                                </div>
                            </li>
                            <li class="item">
                                <a href="#" class="box-icon w-52"><i class="icon icon-bathtub"></i></a>
                                <div class="content">
                                    <span class="label">Bathrooms:</span>
                                    <span>{{ $unit->no_of_bathrooms }} Rooms</span>
                                </div>
                            </li>
                            <li class="item">
                                <a href="#" class="box-icon w-52"><i class="icon icon-garage"></i></a>
                                <div class="content">
                                    <span class="label">Garages:</span>
                                    <span>{{ $unit->parking_spaces ?? 0 }} Rooms</span>
                                </div>
                            </li>
                            <li class="item">
                                <a href="#" class="box-icon w-52"><i class="icon icon-ruler"></i></a>
                                <div class="content">
                                    <span class="label">Size:</span>
                                    <span>{{ $unit->size ?? ($unit->builtup_area ?? '-') }} SqFt</span>
                                </div>
                            </li>
                            <li class="item">
                                <a href="#" class="box-icon w-52"><i class="icon icon-hammer"></i></a>
                                <div class="content">
                                    <span class="label">Listind Date:</span>
                                    <span> {{ optional(\Carbon\Carbon::parse($unit->listing_date))->format('d/m/Y') }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    

                    <div class="single-property-element single-property-info">
                        <div class="h7 title fw-7">Property Details</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Property ID:</span>
                                    <div class="content fw-7">{{ $unit->code ?? $unit->unit_number }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Bedrooms:</span>
                                    <div class="content fw-7">{{ $unit->bedrooms ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Price:</span>
                                    <div class="content fw-7">
                                        {{ $unit->currency_symbol }} {{ $unit->formatted_price }}
                                        <span class="caption-1 fw-4 text-variant-1">
                                            {{ $unit->is_for_sale ? '' : '/month' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Bathrooms:</span>
                                    <div class="content fw-7">{{ $unit->no_of_bathrooms ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Property Size:</span>
                                    <div class="content fw-7">
                                        {{ $unit->size ?? ($unit->builtup_area ?? 'N/A') }}
                                        {{ $unit->area_measurement ?? 'SqFt' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Year Built:</span>
                                    <div class="content fw-7">{{ $unit->handover_date ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Property Type:</span>
                                    <div class="content fw-7">{{ $unit->category ?? optional($unit->unitType)->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Garage:</span>
                                    <div class="content fw-7">{{ $unit->parking_spaces ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Unit View:</span>
                                    <div class="content fw-7">{{ $unit->primary_unit_view }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Garage Size:</span>
                                    <div class="content fw-7">{{ $unit->parking_number ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="single-property-element single-property-feature">
                        <div class="h7 title fw-7">Amenities</div>
                        <div class="wrap-feature">
                            <div class="box-feature">
                                
                                <ul>
                                   
                                    @if($unit->fittings->count())
    <div class="mt-4">
        
        <ul>
            @foreach($unit->fittings as $fit)
                <li>- {{ $fit->name }}</li>
           
                                    @endforeach
        </ul>
    </div>
    @else
     No Amenities
    @endif

                                </ul>
                            </div>
                        </div>
                    </div>

                    @if ($unit->latitude && $unit->longitude)
                        <div class="single-property-element single-property-map">
                            <div class="h7 title fw-7">Map</div>

                            <div id="map-single" class="map-single" data-lat="{{ $unit->latitude }}"
                                data-lng="{{ $unit->longitude }}" data-title="{{ $unit->property_name }}">
                            </div>

                            <ul class="info-map">
                                <li>
                                    <div class="fw-7">Address</div>
                                    <span class="mt-4 text-variant-1">
                                        {{ $unit->street ?? '' }}
                                        {{ $unit->city_name ?? '' }},
                                        {{ optional($unit->country)->name ?? '' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    @endif





                    <div class="single-property-element single-property-explore">
                        <div class="h7 title fw-7">Explore Property</div>
                        <div class="box-img">
                            <img src="{{ asset('frontend/images/banner/img-explore.jpg') }}" alt="img">
                            <div class="box-icon w-80">
                                <span class="icon icon-360"></span>
                            </div>
                        </div>
                    </div>





                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="widget-sidebar fixed-sidebar wrapper-sidebar-right">
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


    
                                
                                @php
                                    $countryCode = session('country_code');

                                    if ($countryCode === 'in' || $countryCode === null) {
                                        $mb = '+91-9211331048'; 
                                    } else {
                                        $mb = '+971 58 586 2033';
                                    }
                                @endphp
                                <div class="info">
                                    <div class="text-1 name">Vserve Real Estate</div>
                                    <span>{{$mb}} Info@vserverealestate.com</span>
                                </div>
                            </div>
                            <form action="{{ route('contact.inquiries.store') }}" method="post" class="contact-form">
                                @csrf
                                <input type="hidden" name="type" value="properties" />
                                <input type="hidden" name="p_id" value="{{ $id }}" />
                                <div class="ip-group">
                                    <label>Full Name:</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required oninput="this.value = this.value.replace(/[^A-z .]/g, '').replace(/(\..*?)\..*/g, '$1');"  >
                                    <span class="text-danger">
                                        @error('name') {{ $message }} @enderror
                                    </span>
                                </div>

                                <div class="ip-group">
                                    <label>Phone Number:</label>
                                    <input type="text" name="phone_no" class="form-control" value="{{ old('phone_no') }}" required oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" maxlength="12">
                                    <span class="text-danger">
                                        @error('phone_no') {{ $message }} @enderror
                                    </span>
                                </div>

                                <div class="ip-group">
                                    <label>Email Address:</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                    <span class="text-danger">
                                        @error('email') {{ $message }} @enderror
                                    </span>
                                </div>

                                <div class="ip-group">
                                    <label>Your Message:</label>
                                    <textarea name="message" rows="4" class="form-control" required>{{ old('message') }}</textarea>
                                    <span class="text-danger">
                                        @error('message') {{ $message }} @enderror
                                    </span>
                                </div>

                                <button class="tf-btn primary w-100">Send Message</button>
                                <span class="text-success d-block mt-2 text-center">
                                    @if(session('success'))
                                        We have received your inquiry. Our team will contact you shortly.
                                    @endif
                                </span> 
                                <span class="text-danger d-block mt-2 text-center">
                                    @if(session('error'))
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
    </section>
    <section class="flat-section pt-0 flat-latest-property">
        <div class="container">
            <div class="box-title">
                <div class="text-subtitle text-primary">Featured properties</div>
                <h4 class="mt-4">The Most Recent Estate</h4>
            </div>
            <div class="swiper tf-latest-property" data-preview-lg="3" data-preview-md="2" data-preview-sm="2"
                data-space="30" data-loop="true">

                <div class="swiper-wrapper">

                    @forelse ($latestUnits as $item)
                        <div class="swiper-slide">
                            <div class="homeya-box style-2">
                                <div class="archive-top">
                                    {{-- <a href="{{ route('property.details', Crypt::encrypt($item->id)) }}" class="images-group"> --}}
                                    <a href="{{ $unit->slug ? route('property.details', $unit->slug) : '#' }}" class="images-group">
                                        <div class="images-style">
                                            <img src="{{ asset('storage/' . ($item->images[0] ?? 'placeholder.jpg')) }}" class="feature_height"
                                                alt="{{ $item->property_name }}">
                                        </div>

                                        <div class="top">
                                            <ul class="d-flex gap-8">
                                                @if ($item->is_featured)
                                                    <li class="flag-tag success">Featured</li>
                                                @endif
                                                <li class="flag-tag style-1">
                                                    {{ $item->is_for_sale ? 'For Sale' : 'For Rent' }}
                                                </li>
                                            </ul>

                                            <ul class="d-flex gap-4">
                                                <li class="box-icon w-32"><span class="icon icon-arrLeftRight"></span>
                                                </li>
                                                <li class="box-icon w-32"><span class="icon icon-heart"></span></li>
                                                <li class="box-icon w-32"><span class="icon icon-eye"></span></li>
                                            </ul>
                                        </div>

                                        <div class="bottom">
                                            <span class="flag-tag style-2">
                                                {{ optional($item->unitType)->name ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </a>

                                    <div class="content">
                                        <div class="h7 text-capitalize fw-7">
                                            <a href="{{ route('property.details', Crypt::encrypt($item->id)) }}"
                                                class="link">
                                                {{ $item->property_name }}
                                            </a>
                                        </div>

                                        <div class="desc">
                                            <i class="fs-16 icon icon-mapPin"></i>
                                            <p>
                                                {{ $item->street ?? '' }}
                                                {{ $item->city_name ?? '' }},
                                                {{ optional($item->country)->name ?? '' }}
                                            </p>
                                        </div>

                                        <ul class="meta-list">
                                            <li class="item">
                                                <i class="icon icon-bed"></i>
                                                <span>{{ $item->bedrooms ?? 0 }}</span>
                                            </li>
                                            <li class="item">
                                                <i class="icon icon-bathtub"></i>
                                                <span>{{ $item->no_of_bathrooms ?? 0 }}</span>
                                            </li>
                                            <li class="item">
                                                <i class="icon icon-ruler"></i>
                                                <span>
                                                    {{ $item->size ?? ($item->builtup_area ?? 0) }}
                                                    {{ $item->area_measurement ?? 'SqFt' }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="archive-bottom d-flex justify-content-between align-items-center">
                                    <div class="d-flex gap-8 align-items-center">
                                        <div class="avatar avt-40 round">
                                            <img src="{{ asset('assets/images/defalt/dummy_property_images.png') }}" alt="agent">
                                        </div>
                                        <span>{{ optional($item->developer)->name ?? 'Developer' }}</span>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <h6>
                                            {{ $item->currency_symbol }} 
                                            {{ priceShort($item->selling_price) }}
                                            {{-- {{ $item->formatted_price }} --}}
                                        </h6>
                                        @if (!$item->is_for_sale)
                                            <span class="text-variant-1">/month</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    @empty
                        <div class="swiper-slide">
                            <p class="text-muted">No featured properties found</p>
                        </div>
                    @endforelse

                </div>

            </div>
        </div>
    </section>
   
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Fancybox.bind("[data-fancybox='property-gallery']", {
                Thumbs: {
                    autoStart: true,
                },
                Toolbar: {
                    display: [{
                            id: "prev",
                            position: "center"
                        },
                        {
                            id: "counter",
                            position: "center"
                        },
                        {
                            id: "next",
                            position: "center"
                        },
                        "zoom",
                        "fullscreen",
                        "download",
                        "thumbs",
                        "close",
                    ],
                },
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const mapEl = document.getElementById('map-single');
            if (!mapEl) return;

            let lat = parseFloat(mapEl.dataset.lat);
            let lng = parseFloat(mapEl.dataset.lng);
            let title = mapEl.dataset.title ?? '';

            if (isNaN(lat) || isNaN(lng)) {
                console.warn('Invalid latitude / longitude');
                return;
            }

            let map = L.map('map-single').setView([lat, lng], 15);

            // ✅ FREE CARTO MAP (NO AUTH, NO API KEY)
            L.tileLayer(
                'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors &copy; CARTO'
                }
            ).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup(title)
                .openPopup();

            // Fix for hidden/accordion layouts
            setTimeout(() => {
                map.invalidateSize();
            }, 300);
        });
    </script>
@endsection
