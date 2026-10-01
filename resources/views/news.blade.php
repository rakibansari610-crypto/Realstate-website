
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
</style>
    <!-- Latest New -->
    @if($news && $news->count() > 0)
    <section class="flat-section flat-latest-new wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
        <div class="container">
            <div class="box-title text-center">
                <div class="text-subtitle text-primary">Latest News</div>
                <h4 class="mt-4">Helpful Homeya Guides</h4>
            </div>
            <div class="row mb-4">
                <div class="col-md-8 mx-auto">
                    <div class="position-relative">
                        <input type="text" id="searchNews" class="form-control ps-5" placeholder="Search news...">
                        
                        <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                            <i class="fa fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row" id="newsContainer">
                @foreach ($news as $blog)
                    <div class="box col-lg-4 col-md-6 mb-5">
                        <a href="{{ route('news-details', $blog->slug) }}" class="flat-blog-item hover-img">

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
                            </div>
                            <div class="content-box">
                                <div class="post-author">
                                    <span>{{ ucwords($blog->category ?? '') ?: "\u{00A0}" }}</span>
                                </div>

                                <h6 class="title two-line">{{ ucwords($blog->title) }}</h6>

                                <p class="description two-line">
                                    {{ $blog->short_description }}
                                </p>
                            </div>

                        </a>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-5">
                {{ $news->links() }}
            </div>

        </div>
    </section>
    @endif<!-- End Latest New -->
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


        $('#searchNews').on('keyup', function () {
            console.log('Search input changed');
            let keyword = $(this).val();

            $.ajax({
                url: "{{ route('news.search') }}",
                type: "GET",
                data: { search: keyword },

                success: function (response) {
                    $('#newsContainer').html(response);
                }
            });
        });


});
</script>

@endsection
