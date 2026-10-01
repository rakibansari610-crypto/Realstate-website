@extends('layouts.frontend.frontend_layout')
@section('meta_title')
{{ $blog->meta_title ?? $blog->title }}
@endsection

@section('meta_keywords')
{{ $blog->meta_keywords ?? 'real estate blog, dubai property news' }}
@endsection

@section('meta_description')
{{ $blog->meta_description ?? Str::limit(strip_tags($blog->short_description),160) }}
@endsection
@section('main-content')
<style>
    .two-line{
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 22px;
            min-height: 44px; /* 2 lines ka space */
        }
    /* Global Blog Styling */
    .blog-hero {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    /* .blog-hero img {
        width: 100%;
        height: 500px;
        object-fit: cover;
    } */

    .blog-hero img {
        width: 100%;
        height: auto;
        display: block;
    }
    .blog-meta {
        display: flex;
        gap: 25px;
        margin: 25px 0;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
        color: #666;
        font-weight: 500;
    }
    .blog-meta i {
        color: #007bff; /* Primary Color */
        margin-right: 8px;
    }

    /* Content Styling */
    /* .blog-content {
        font-size: 1.1rem;
        line-height: 1.9;
        color: #333;
    }
    .blog-content h2, .blog-content h3 {
        margin-top: 30px;
        font-weight: 700;
    } */
     /* .blog-content {
        font-size: 1.1rem;
        line-height: 1.5;
        color: #333;
    }
    */

    .blog-content {
        font-size: 1.1rem;
        line-height: 1.5;
        color: #333;
    }

    .blog-content h2,
    .blog-content h3 {
        margin: 12px 0 8px;
        font-weight: 700;
        line-height: 1.3;
    }

    .blog-content p {
        margin: 0 0 10px;
        line-height: 1.5;
        font-size: 16px;
    }
    .blog-content ul li 
    {
        font-size: 16px;
    }  
    .blog-content p a 
    {
        color: #007bff !important;
    }
    .blog-content p a:hover 
    {
        text-decoration: underline;
    }
   /*  .blog-content h2,
    .blog-content h3 {
        margin-top: 30px;
        margin-bottom: 12px;
        font-weight: 700;
        line-height: 1.25;
    } */
    .blog-content blockquote {
        border-left: 5px solid #007bff;
        padding: 20px;
        background: #f8f9fa;
        font-style: italic;
        border-radius: 0 10px 10px 0;
        margin: 30px 0;
    }

    /* Sidebar Styling */
    .widget-sidebar {
        position: sticky;
        top: 100px;
    }
    .widget-box {
        background: #fff;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        margin-bottom: 30px;
        border: 1px solid #f1f1f1;
    }
    .widget-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
        display: inline-block;
    }

    /* Recent Posts */
    .recent-post-item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        transition: 0.3s;
    }
    .recent-post-item:hover {
        transform: translateX(5px);
    }
    .recent-post-item img {
        width: 70px;
        height: 70px;
        border-radius: 12px;
        object-fit: cover;
    }
    .recent-post-content {
        padding-left: 15px;
    }
    .recent-post-content a {
        font-weight: 600;
        color: #222;
        line-height: 1.4;
        display: block;
        margin-bottom: 5px;
        text-decoration: none;
    }

    /* Related Cards */
    .blog-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
        transition: all 0.4s ease;
        height: 100%;
        background: #fff;
    }
    .blog-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.12);
    }
    .blog-card img {
        height: 220px;
        object-fit: cover;
    }
    .btn-read-more {
        color: #141b29;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 1px;
        text-decoration: none;
    }

    /* Form Styling */
    .sidebar-form input, .sidebar-form textarea {
        background: #f9f9f9;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 15px;
        width: 100%;
    }
</style>

<section class="  mt-5">
    <div class="container">
        <div class="row">
            {{-- LEFT CONTENT --}}
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3" style="color: #1a1a1a;">
                    {{ $blog->title }}
                </h1>
                <div class="blog-meta">
                    <span><i class="fa fa-calendar-alt"></i> {{ $blog->created_at->format('d M, Y') }}</span>
                    <span><i class="fa fa-user"></i> By Vserve Real Estate</span>
                    {{-- <span><i class="fa fa-tag"></i> Informative</span> --}}
                </div>
                <div class="blog-hero mb-5">
                     @php
                        $defaultImage = 'assets/images/defalt/dummy_property_images.png';

                        if (!empty($blog->detailed_image) && Storage::disk('public')->exists($blog->detailed_image)) {
                            $Newsimg = asset('storage/'.$blog->detailed_image);
                        } else {
                            $Newsimg = asset($defaultImage);
                        }
                    @endphp
                    <img src="{{ $Newsimg }}" alt="{{ $blog->title }}">
                </div>
                @if($blog->short_description)
                <div class="lead fw-normal mb-4 text-muted" style="font-size: 1.25rem; border-left: 4px solid #ddd; padding-left: 20px;">
                    {{ $blog->short_description }}
                </div>
                @endif

                <div class="blog-content">
                    {!! $blog->content !!}
                </div>

                {{-- <div class="share-post mt-5 p-4 bg-light rounded-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Share this post:</h6>
                    <div class="social-links">
                        <a href="#" class="btn btn-outline-primary btn-sm rounded-circle mx-1"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-outline-info btn-sm rounded-circle mx-1"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-outline-danger btn-sm rounded-circle mx-1"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div> --}}
            </div>

            {{-- RIGHT SIDEBAR --}}
            <div class="col-lg-4">
                <div class="widget-sidebar">



                    {{-- RECENT BLOGS --}}
                    <div class="widget-box">
                        <div class="widget-title">Latest Articles</div>
                        @foreach($latestBlogs as $rb)
                        <div class="recent-post-item">
                            @php
                                $defaultImage = 'assets/images/defalt/dummy_property_images.png';

                                if (!empty($rb->featured_image) && Storage::disk('public')->exists($rb->featured_image)) {
                                    $rbImg = asset('storage/'.$rb->featured_image);
                                } else {
                                    $rbImg = asset($defaultImage);
                                }
                            @endphp
                            <img src="{{ $rbImg }}" alt="thumb">
                            <div class="recent-post-content">
                                <a href="{{ $rb->slug ? route('news-details', $rb->slug) : '#' }}">
                                    {{ Str::limit($rb->title, 45) }}
                                </a>
                                <span class="small text-muted">{{ $rb->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>


                </div>
            </div>
        </div>
    </div>
</section>

{{-- RELATED BLOGS --}}
@if($relatedBlogs->count())
<section class="flat-section bg-light py-5 mt-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold mb-0">Related Articles</h3>
            <div class="line" style="flex-grow: 1; height: 1px; background: #ddd; margin-left: 20px;"></div>
        </div>
        <div class="row">
            @foreach($relatedBlogs as $r)
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="homeya-box md">
                    <div class="archive-top">
                        <a href="{{ $r->slug ? route('news-details', $r->slug) : '#' }}" class="images-group">
                            <div class="images-style">
                                @php
                                    $defaultImage = 'assets/images/defalt/dummy_property_images.png';

                                    if (!empty($r->featured_image) && Storage::disk('public')->exists($r->featured_image)) {
                                        $rlimg = asset('storage/'.$r->featured_image);
                                    } else {
                                        $rlimg = asset($defaultImage);

                                    }
                                @endphp
                                <img src="{{ $rlimg }}" alt="" style="height:230px;">
                            </div>
                        </a>
                        <div class="content">
                            <div class="text-1 text-capitalize">
                                <a href="{{ $r->slug ? route('news-details', $r->slug) : '#' }}" class="link property-text" > {{ $r->title ?? 'Property' }} </a>
                            </div>
                            <div class="desc">
                                <p class="two-line"> {{$r->short_description ?? 'Description'}} </p>
                            </div>
                            <a href="{{ $r->slug ? route('news-details', $r->slug) : '#' }}"  class="btn-read-more">
                                Read Article <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@section('scripts')
<script>
    $(document).ready(function(){
    $('.sidebar-form').on('submit', function(e){
        e.preventDefault();
    });

    $('#searchInput').on('keyup', function(){

        let query = $(this).val();

        if(query.length < 2){
            $('#searchResults').hide();
            return;
        }

        $.ajax({
            url: "{{ route('search.blog') }}",
            type: "GET",
            data: { q: query,
                    slug: "{{ $blog->slug }}"
             },

            success: function(data){

                let html = '';

                if(data.length > 0){

                    let blogUrl = "{{ route('news-details', ':slug') }}";

                    data.forEach(function(item){

                        let finalUrl = blogUrl.replace(':slug', item.slug);

                        html += `
                            <div class="recent-post-item">

                                <img src="${item.image_url}" alt="thumb">

                                <div class="recent-post-content">
                                    <a href="${finalUrl}">
                                        ${item.title.substring(0,45)}
                                    </a>
                                    <span class="small text-muted">
                                        ${item.created_at.substring(0,10)}
                                    </span>
                                </div>

                            </div>
                        `;
                    });

                } else {
                    html = `<div class="search-item">No results found</div>`;
                }

                $('#searchResults').html(html).show();
            }
        });

    });

});
</script>

@endsection
