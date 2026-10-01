@extends('layouts.frontend.frontend_layout')
@section('main-content')
@php
$country = request()->route('country') ?? session('country', 'in');
@endphp
    <section class="flat-section-v6 flat-recommended flat-sidebar">
        <div class="container">
            <div class="box-title-listing">
                @if(isset($type) && $type == 'buy')
                <h1>Properties on Sale </h1>
                @else
                <h1>Properties on Rent </h1>
                @endif
                
            </div>
            <div class="row">
                <div class="col-xl-4 col-lg-5">
                    <div class="widget-sidebar fixed-sidebar">
                        <div class="flat-tab flat-tab-form widget-filter-search widget-box bg-surface">
                            <div class="h7 title fw-7">Search </div>
                            <ul class="nav-tab-form" role="tablist">
                                @if(isset($type) && $type == 'rent')
                                <li class="nav-tab-item" role="presentation">
                                    <a href="#forRent" class="nav-link-item active" data-bs-toggle="tab">For Rent</a>
                                </li>
                                @endif
                                @if(isset($type) && $type == 'buy')

                                <li class="nav-tab-item" role="presentation">
                                    <a href="#forSale" class="nav-link-item active" data-bs-toggle="tab">For Sale</a>
                                </li>
                                @endif
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade active show" role="tabpanel">
                                    <div class="form-sl">
                                        <form id="filterForm" onsubmit="return false;">
                                            <div class="wd-filter-select">
                                                <div class="inner-group inner-filter">
                                                    {{-- UNIT TYPE --}}
                                                    <div class="form-style">
                                                        <label class="title-select">Developers</label>
                                                        <select name="developer_id" class="form-control">
                                                            <option value="">All</option>
                                                            @foreach ($developer as $dp)
                                                                <option value="{{ $dp->id }}"
                                                                    {{ request('developer_id') == $dp->id ? 'selected' : '' }}>
                                                                    {{ $dp->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    {{-- KEYWORD --}}
                                                    <div class="form-style">
                                                        <label class="title-select">Keyword</label>
                                                        <input type="text" name="keyword" class="form-control"
                                                            placeholder="Search Keyword" value="{{ request('keyword') }}">
                                                    </div>


                                                    {{-- LOCATION --}}
                                                    <div class="form-style">
                                                        <label class="title-select">City</label>
                                                        <div class="group-ip ip-icon">
                                                            <input type="text" name="city" class="form-control"
                                                                placeholder="Search Location" value="{{ request('city') }}">
                                                            <span class="icon-right icon-location"></span>
                                                        </div>
                                                    </div>
                                                    {{-- LOCATION --}}
                                                    <div class="form-style">
                                                        <label class="title-select">Locality</label>
                                                        <div class="group-ip ip-icon">
                                                            <input type="text" name="locality" class="form-control"
                                                                placeholder="Search Locality" value="{{ request('locality') }}">
                                                            <span class="icon-right icon-location"></span>
                                                        </div>
                                                    </div>

                                                    {{-- UNIT TYPE --}}
                                                    <div class="form-style">
                                                        <label class="title-select">Type</label>
                                                        <select name="unit_type" class="form-control">
                                                            <option value="">All</option>
                                                            @foreach ($unitTypes as $type)
                                                                <option value="{{ $type->id }}"
                                                                    {{ request('unit_type') == $type->id ? 'selected' : '' }}>
                                                                    {{ $type->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    {{-- BATHROOMS --}}
                                                    <div class="form-style box-select">
                                                        <label class="title-select">Bathrooms</label>
                                                        <input type="hidden" name="bathrooms"
                                                            value="{{ request('bathrooms') }}">

                                                        <div class="nice-select js-bathrooms" tabindex="0">
                                                            <span class="current">{{ request('bathrooms') ?? 'All' }}</span>
                                                            <ul class="list">
                                                                <li data-value="" class="option">All</li>
                                                                @for ($i = 1; $i <= 10; $i++)
                                                                    <li data-value="{{ $i }}"
                                                                        class="option {{ request('bathrooms') == $i ? 'selected' : '' }}">
                                                                        {{ $i }}
                                                                    </li>
                                                                @endfor
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    {{-- BEDROOMS --}}
                                                    <div class="form-style box-select">
                                                        <label class="title-select">Bedrooms</label>
                                                        <input type="hidden" name="bedrooms"
                                                            value="{{ request('bedrooms') }}">

                                                        <div class="nice-select js-bedrooms" tabindex="0">
                                                            <span
                                                                class="current">{{ request('bedrooms') ?? 'All' }}</span>
                                                            <ul class="list">
                                                                <li data-value="" class="option">All</li>
                                                                @for ($i = 1; $i <= 10; $i++)
                                                                    <li data-value="{{ $i }}"
                                                                        class="option {{ request('bedrooms') == $i ? 'selected' : '' }}">
                                                                        {{ $i }}
                                                                    </li>
                                                                @endfor
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    {{-- PRICE RANGE --}}
                                                    <div class="form-style widget-price">
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

                                                        <input type="hidden" name="min_price"
                                                            value="{{ request('min_price') }}">
                                                        <input type="hidden" name="max_price"
                                                            value="{{ request('max_price') }}">
                                                    </div>

                                                    {{-- SUBMIT --}}
                                                    <div class="form-style">
                                                        <button type="submit" class="tf-btn primary">
                                                            Find Properties
                                                        </button>
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
                <div class="col-xl-8 col-lg-7">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="gridLayout" role="tabpanel">
                            
                            <div id="properties-wrapper">
                                @include('properties.properties_card', ['units' => $units])
                            </div>


                         


                        </div>
                              </div>
                </div>
            </div>



        </div>
    </section>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {

            function getCleanFormData() {
                let data = {};
                $('#filterForm').find('input, select').each(function() {
                    let name = $(this).attr('name');
                    let val = $(this).val();
                    if (val !== '' && val !== null) {
                        data[name] = val;
                    }
                });
                return data;
            }

            function fetchProperties(url = "{{ route('properties', ['type' => $type]) }}") {
                $.ajax({
                    url: url,
                    type: "GET",
                    data: getCleanFormData(),
                    beforeSend: function() {
                        $('#properties-wrapper').html(
                            '<p class="text-center">Loading...</p>'
                        );
                    },
                    success: function(response) {
                        $('#properties-wrapper').html(response);
                    }
                });
            }

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                fetchProperties();
            });

            $(document).on('click', '#properties-wrapper .pagination a', function(e) {
                e.preventDefault();
                fetchProperties($(this).attr('href'));
            });


            $(document).on('click', '.js-bathrooms .option', function() {
                let val = $(this).data('value');
                $('input[name="bathrooms"]').val(val);
                $('.js-bathrooms .current').text(val || 'All');
            });

            $(document).on('click', '.js-bedrooms .option', function() {
                let val = $(this).data('value');
                $('input[name="bedrooms"]').val(val);
                $('.js-bedrooms .current').text(val || 'All');
            });


            if ($.ui && $.ui.slider) {

                let minPrice = {{ request('min_price', 0) }};
                let maxPrice = {{ request('max_price', 10000000) }};

                // initial text set
                $('#slider-range-value1').text(minPrice);
                $('#slider-range-value2').text(maxPrice);

                $("#slider-range").slider({
                    range: true,
                    min: 0,
                    max: 10000000,
                    values: [minPrice, maxPrice],
                    slide: function(e, ui) {
                        $('#slider-range-value1').text(ui.values[0]);
                        $('#slider-range-value2').text(ui.values[1]);
                        $('input[name="min_price"]').val(ui.values[0]);
                        $('input[name="max_price"]').val(ui.values[1]);
                    }
                });
            }

        });
    </script>
@endsection
