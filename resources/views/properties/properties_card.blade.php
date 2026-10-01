<style>
    .img-hgt{    height: 316px; }
    .single-line-ellipsis {
    display: block;        /* important */
    width: 100%;           /* container ke according */
    white-space: nowrap;   /* line break band */
    overflow: hidden;      
    text-overflow: ellipsis;
}
</style>
@php
$country = request()->route('country') ?? session('country', 'in');
@endphp
<div class="row">
    @forelse ($units as $unit)

@if(!empty($unit->slug))

@php
    $defaultImage = asset('assets/images/defalt/dummy_property_images.png');
    $unitImage = $unit->unit_image ?: $defaultImage;
@endphp

<div class="col-md-6">
    <div class="homeya-box">

        <div class="archive-top">

            <a href="{{ route('property.details', ['country'=>$country,'slug'=>$unit->slug]) }}" class="images-group">

                <div class="images-style">
                    
                    <img src="{{ $unitImage }}" alt="{{ $unit->property_name }}" class="img-hgt">
                </div>

                <div class="top">

                    <ul class="d-flex gap-8">
                        @if ($unit->is_featured)
                            <li class="flag-tag success">Featured</li>
                        @endif

                       <li class="flag-tag style-1">{{ $unit->unit_for }}</li>
                    </ul>

            
                </div>

                <div class="bottom">
                    <span class="flag-tag style-2">
                        {{ $unit->category ?? '' }}
                    </span>
                </div>

            </a>

            <div class="content">

                <div class="h7 text-capitalize fw-7">
                    <a href="{{ route('property.details',['country'=>$country,'slug'=>$unit->slug]) }}" 
                       class="link single-line-ellipsis">
                        {{ $unit->property_name }}
                    </a>
                </div>

                <div class="desc">
                    <i class="fs-16 icon icon-mapPin"></i>
                    <p>
                        {{ $unit->city_name ?? '' }},
                        {{ $unit->country?->country_name ?? '' }}
                    </p>
                </div>

               <ul class="meta-list">

    @if(!empty($unit->bedrooms))
    <li class="item">
        <i class="icon icon-bed"></i>
        <span>{{ $unit->bedrooms }}</span>
    </li>
    @endif

    @if(!empty($unit->no_of_bathrooms))
    <li class="item">
        <i class="icon icon-bathtub"></i>
        <span>{{ $unit->no_of_bathrooms }}</span>
    </li>
    @endif

    @if(!empty($unit->size))
    <li class="item">
        <i class="icon icon-ruler"></i>
        <span>{{ $unit->size }} SqFT</span>
    </li>
    @endif

</ul>

            </div>
        </div>


        <div class="archive-bottom d-flex justify-content-between align-items-center">

            <span>Area: {{ $unit->builtup_area ?? '' }} SqFT</span>

            <div class="d-flex align-items-center">

                <div class="h7 fw-7">

                    @php
                        $countryCode = session('country_code');
                        $currency = $countryCode === 'in' ? '₹' : 'AED';
                    @endphp

                    {{ $currency }} {{ priceShort($unit->selling_price ?? $unit->rent_price) }}

                </div>

               

            </div>

        </div>

    </div>
</div>

@endif

@empty
<div class="col-12 text-center">
    <h5>No properties found</h5>
</div>
@endforelse
</div>

    <div class="d-flex justify-content-center mt-4">
                        {{ $units->onEachSide(1)->links('pagination.wd-navigation') }}
                    </div>