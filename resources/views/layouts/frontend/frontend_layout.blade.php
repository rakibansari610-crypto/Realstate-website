<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">

<head>
    <meta charset="utf-8">
    <title>@yield('meta_title','Vserve Real Estate')</title>

    {{-- <meta name="keywords" content="@yield('meta_keywords','Dubai real estate, UAE property, buy property')">
    <meta name="description" content="@yield('meta_description','Find the best real estate properties in Dubai and UAE with Vserve Real Estate.')">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large"> --}}
    @if(!empty($seo['meta_title'] ?? null))
        <meta name="title" content="{{ $seo['meta_title'] }}">
    @else
        <meta name="title" content="Dubai Real Estate | Buy Property in UAE">
    @endif
    
    @if(!empty($seo['keywords'] ?? null))
        <meta name="keywords" content="{{ $seo['keywords'] }}">
    @else
        <meta name="keywords" content="Dubai real estate, UAE property, buy property">
    @endif

    @if(!empty($seo['description'] ?? null))
        <meta name="description" content="{{ $seo['description'] }}">
    @else
        <meta name="description" content="Find the best real estate properties in Dubai and UAE with Vserve Real Estate.">
    @endif 
    @if(!empty($seo['og_image']))
      <meta property="og:image" content="{{ url($seo['og_image']) }}">
      @if(!empty($seo['og_image_alt']))
          <meta property="og:image:alt" content="{{ $seo['og_image_alt'] }}">
      @endif
    @endif
    @if(!empty($seo['og_title']))
      <meta property="og:title" content="{{ $seo['og_title'] ?? $seo['meta_title'] ?? '' }}">
    @endif
    @if(!empty($seo['og_description']))
      <meta property="og:description" content="{{ $seo['og_description'] }}">
    @endif

    @foreach(config('seo.hreflang_urls', []) as $lang => $url)
        @if(!empty($url))
            <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}" />
        @endif
    @endforeach

    <link rel="canonical" href="@yield('canonical', request()->fullUrl())" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="{{ config('seo.author') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="{{ asset('frontend/fonts/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/fonts/font-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/css/styles.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    .custom-pagination {
    gap: 6px;
}

.custom-pagination .page-link {
    border: 1px solid #ddd;
    padding: 8px 14px;
    border-radius: 6px;
    color: #2c3e50;
    background: #f8f9fa;
    transition: 0.2s;
}

.custom-pagination .page-link:hover {
    background: #e9ecef;
}

.custom-pagination .active .page-link {
    background: #ff3b30;
    color: #fff;
    border-color: #ff3b30;
}

.custom-pagination .prev-btn,
.custom-pagination .next-btn {
    font-weight: 500;
}

.custom-pagination .dots {
    background: transparent;
    border: none;
}
        .map-single {
            height: 350px;
            width: 100%;
            border-radius: 12px;
        }
        .white {
            color: #fff;
        }
       .blog-img-box{
    aspect-ratio: 16 / 9;
    overflow: hidden;
}

.blog-img-box img{
    width:100%;
    height:100%;
    object-fit:cover;
}
h1 { font-size: 2.5rem; }  /* 40px */
h2 { font-size: 2rem; }    /* 32px */
h3 { font-size: 1.75rem; } /* 28px */
h4 { font-size: 1.5rem; }  /* 24px */
h5 { font-size: 1.25rem; } /* 20px */
h6 { font-size: 1rem; }    /* 16px */




h2.title {
    font-size: 2.5rem;
    font-weight: bold;
}
/* //  mobile menu country change css  */
.country-dropdown {
    position: absolute;
    top: 30px;
    right: 0;
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    display: none;
    min-width: 130px;
    z-index: 9999;
}

.country-dropdown a {
    display: block;
    padding: 10px;
    color: #000;
    text-decoration: none;
}

.country-dropdown a:hover {
    background: #f5f5f5;
}

.mobile-globe {
    display: none;
    margin-right: 10px;
    position: relative;
}

@media (max-width: 991px) {
    .mobile-globe {
        display: block;
         margin-left: 10px;
    }

    .nav-outer,
    .header-account {
        display: none; /* mobile me hide */
    } 
    .inner-container {
        justify-content: flex-start !important;
    }

     

    .mobile-globe {
        margin-left: auto;
        margin-right: 50px;
    }

    .mobile-nav-toggler {
        margin-left: 0;
    }
    h2.title {
    font-size: 2rem;
}
}

@media (min-width: 1200px) {
    .display-5 {
        font-size: 2rem;
    }
}



    </style>

    <script>
        (function(w,d,s,l,i){
            w[l]=w[l]||[];
            w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});
            var f=d.getElementsByTagName(s)[0], j=d.createElement(s), dl=l!='dataLayer'?'&l='+l:'';
            j.async=true;
            j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;
            f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MRJLXZJH');
    </script>
</head>

<body class="body">
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MRJLXZJH"
        height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>

@php
    $country = request()->route('country')
        ?? request()->segment(1)
        ?? session('country')
        ?? session('country_code')
        ?? 'in';

    if (!in_array($country, ['in', 'ae'])) {
        $country = session('country') ?? session('country_code') ?? 'in';
    }

    $countryCode = $country;
    $countryName = $country === 'in' ? 'India' : 'UAE';

    $instagramUrl = $country === 'in'
        ? 'https://www.instagram.com/vserverealestate.india?igsh=NDFlbnBia2d3Znly'
        : 'https://www.instagram.com/vserverealestate.dxb?igsh=NDNucHJiODUxemk2';

    $youtubeUrl = $country === 'in'
        ? 'https://www.youtube.com/@VserveRealEstateIndia'
        : 'https://www.youtube.com/@vserveRealEstateLLC/shorts';
@endphp

<div class="preload preload-container">
    <div class="boxes">
        <div class="box"><div></div><div></div><div></div><div></div></div>
        <div class="box"><div></div><div></div><div></div><div></div></div>
        <div class="box"><div></div><div></div><div></div><div></div></div>
        <div class="box"><div></div><div></div><div></div><div></div></div>
    </div>
</div>

<div id="wrapper">
    <div id="pagee" class="clearfix">

        <header class="main-header fixed-header">
            <div class="header-lower">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="inner-container d-flex justify-content-between align-items-center">

                            <div class="logo-box flex d-flex align-items-center">
                                <div class="logo">
                                    <a href="{{ url($country) }}">
                                        <img src="{{ asset('assets/images/logo.png') }}" alt="logo" width="174" height="44">
                                    </a>
                                </div>
                            </div>
                            

                            <div class="nav-outer">
                                <nav class="main-menu show navbar-expand-md">
                                    <div class="navbar-collapse collapse clearfix" id="navbarSupportedContent">
                                        <ul class="navigation clearfix">
                                            @php
                                                $dynamicHeaderMenus = \App\Models\Menu::whereNull('parent_id')
                                                    ->where('is_active', 1)
                                                    ->with(['children' => function($q) {
                                                        $q->where('is_active', 1)->orderBy('order', 'asc');
                                                    }])
                                                    ->orderBy('order', 'asc')
                                                    ->get();
                                            @endphp

                                            @if(isset($dynamicHeaderMenus) && $dynamicHeaderMenus->count() > 0)
                                                @foreach($dynamicHeaderMenus as $dynamicMenu)
                                                    @if($dynamicMenu->children && $dynamicMenu->children->count() > 0)
                                                        <li class="dropdown2">
                                                            <a href="{{ $dynamicMenu->getFormattedUrl($country ?? 'in') }}" target="{{ $dynamicMenu->target ?? '_self' }}">{{ $dynamicMenu->title }}</a>
                                                            <ul>
                                                                @foreach($dynamicMenu->children as $childMenu)
                                                                    <li>
                                                                        <a href="{{ $childMenu->getFormattedUrl($country ?? 'in') }}" target="{{ $childMenu->target ?? '_self' }}">{{ $childMenu->title }}</a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a href="{{ $dynamicMenu->getFormattedUrl($country ?? 'in') }}" target="{{ $dynamicMenu->target ?? '_self' }}">{{ $dynamicMenu->title }}</a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            @else
                                                <li>
                                                    <a href="{{ route('buy.properties', ['country' => $country]) }}">Buy</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('rent.properties', ['country' => $country]) }}">Rent</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('project', ['country' => $country]) }}">Projects</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('developers', ['country' => $country]) }}">Developers</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('services') }}">Services</a>
                                                </li>

                                                <li class="dropdown2">
                                                    <a href="">More</a>
                                                    <ul>
                                                        <li><a href="{{ route('about-us') }}">About Us</a></li>
                                                        <li><a href="{{ route('news', ['country' => $country]) }}">News</a></li>
                                                        <li><a href="{{ route('contact-us') }}">Contact Us</a></li>
                                                    </ul>
                                                </li>
                                            @endif

                                            <li class="dropdown2">
                                                <a href="#">{{ $countryName }}</a>
                                                <ul>
                                                    <li class="{{ $country === 'in' ? 'active' : '' }}">
                                                        {{-- <a href="{{ route('set.country', 'in') }}"> --}}
                                                        <a href="javascript:void(0)" onclick="changeCountry('in')">
                                                            India
                                                            @if($country === 'in')
                                                                <span>✓</span>
                                                            @endif
                                                        </a>
                                                    </li>

                                                    <li class="{{ $country === 'ae' ? 'active' : '' }}">
                                                        {{-- <a href="{{ route('set.country', 'ae') }}"> --}}
                                                           <a href="javascript:void(0)" onclick="changeCountry('ae')">
                                                            UAE
                                                            @if($country === 'ae')
                                                                <span>✓</span>
                                                            @endif
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </nav>
                            </div>

                            <div class="header-account">
                                <div class="register">
                                    <ul class="d-flex"></ul>
                                </div>
                                <div class="flat-bt-top">
                                    <a class="tf-btn primary" href="{{ url('/login') }}">Login</a>
                                </div>
                            </div>
                            <div class="mobile-globe">
                               <i class="fa fa-map-marker-alt" onclick="toggleCountryMenu()"></i>
                                <div class="country-dropdown" id="countryDropdown">
                                    <a href="javascript:void(0)" onclick="changeCountry('in')">India</a>
                                    <a href="javascript:void(0)" onclick="changeCountry('ae')">UAE</a>
                                </div>
                            </div>

                            <div class="mobile-nav-toggler mobile-button"><span></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="close-btn"><span class="icon flaticon-cancel-1"></span></div>
            <div class="mobile-menu">
                <div class="menu-backdrop"></div>
                <nav class="menu-box">
                    <div class="nav-logo">
                        <a href="{{ url($country) }}">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="nav-logo" width="174" height="44">
                        </a>
                    </div>
                    <div class="bottom-canvas">
                        <div class="login-box">
                            <a href="#modalLogin" data-bs-toggle="modal">Login</a>
                        </div>
                        <div class="menu-outer"></div>
                        <div class="button-mobi-sell">
                            <a class="tf-btn primary" href="{{ route('properties_search', ['country' => $country]) }}">Property</a>
                        </div>
                        <div class="mobi-icon-box">
                            <div class="box d-flex align-items-center">
                                <span class="icon icon-phone2"></span>
                                <div> +97145469191</div>
                            </div>
                            <div class="box d-flex align-items-center">
                                <span class="icon icon-mail"></span>
                                <div>themesflat@gmail.com</div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </header>

        @yield('main-content')

        <footer class="footer" style="padding-top:0px !important">
            <div class="inner-footer">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-4 col-md-6">
                            <div class="fw-7 text-white">Vserve Real Estate</div>
                            <div class="text-white mt-2" style="text-align: justify;padding-right:25px;">
                                We help clients navigate property decisions with clarity and confidence across India & Dubai. From buying and renting to selling & investments, we focus on practical guidance, market insight & a transparent process.

                                <div class="fw-7 text-white mt-2">
                                    Follow us
                                </div>

                                <ul class="list-social d-flex align-items-center mt-3">
                                    <li>
                                        <a href="https://www.facebook.com/profile.php?id=61554214713716" class="box-icon w-40 social">
                                            <i class="icon icon-facebook"></i>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ $instagramUrl }}" class="box-icon w-40 social" target="_blank">
                                            <i style="color: #fff;" class="bi bi-instagram"></i>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{$youtubeUrl}}"  target="_blank" class="box-icon w-40 social">
                                            <i class="icon icon-youtube"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="footer-cl-1">
                                <div class="fw-7 text-white">India</div>
                                <ul class="mt-12">
                                    <li class="mt-12 d-flex gap-8">
                                        <i class="icon icon-mapPinLine fs-20 text-variant-2"></i>
                                        <p class="text-white">A-167, A Block<br> Sector 63 Noida<br> Uttar Pradesh 201301</p>
                                    </li>
                                    <li class="mt-12 d-flex align-items-center gap-8">
                                        <i class="icon icon-phone2 fs-20 text-variant-2"></i>
                                        <a href="tel:9211331048" class="text-white caption-1"> +91-9211331048</a>
                                    </li>
                                    <li class="mt-12 d-flex align-items-center gap-8">
                                        <i class="icon icon-mail fs-20 text-variant-2"></i>
                                        <p class="text-white"> Info@vserverealestate.com</p>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="footer-cl-1">
                                <div class="fw-7 text-white">Dubai</div>
                                <ul class="mt-12">
                                    <li class="mt-12 d-flex gap-8">
                                        <i class="icon icon-mapPinLine fs-20 text-variant-2"></i>
                                        <p class="text-white">2802, Single Business Tower<br>Business Bay - Dubai <br> United Arab Emirates</p>
                                    </li>
                                    <li class="mt-12 d-flex align-items-center gap-8">
                                        <i class="icon icon-phone2 fs-20 text-variant-2"></i>
                                        <a href="tel:+971585862033" class="text-white caption-1"> +971 58 586 2033</a>
                                    </li>
                                    <li class="mt-12 d-flex align-items-center gap-8">
                                        <i class="icon icon-mail fs-20 text-variant-2"></i>
                                        <p class="text-white"> Info@vserverealestate.com</p>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="footer-cl-3">
                                <div class="fw-7 text-white">Categories</div>

                                <ul class="mt-10 navigation-menu-footer">
                                    <li>
                                        <a href="{{ route('buy.properties', ['country' => $country]) }}" class="caption-1 text-variant-2">
                                            Property For Sale
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ route('rent.properties', ['country' => $country]) }}" class="caption-1 text-variant-2">
                                            Property For Rent
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ route('project', ['country' => $country]) }}" class="caption-1 text-variant-2">
                                            Projects in {{ $countryName }}
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ route('developers', ['country' => $country]) }}" class="caption-1 text-variant-2">
                                            Developers in {{ $countryName }}
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ route('about-us') }}" class="caption-1 text-variant-2">About Us</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="bottom-footer">
                <div class="container">
                    <div class="content-footer-bottom">
                        <div class="copyright">©2026 VSERVE REAL ESTATE. All Rights Reserved.</div>
                        <ul class="menu-bottom">
                            <li><a href="{{ route('terms-conditions') }}">Terms Of Services</a></li>
                            <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>

    </div>
</div>

<div class="progress-wrap">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
            style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 286.138;">
        </path>
    </svg>
</div>

<div class="modal fade" id="modalCountry">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="flat-account bg-surface">
                <div class="title text-center">Select Country</div>
                <span class="close-modal icon-close2" data-bs-dismiss="modal"></span>
                <form action="{{ url('login') }}" method="POST"></form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLogin">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="flat-account bg-surface">
                <div class="title text-center">Log In</div>
                <span class="close-modal icon-close2" data-bs-dismiss="modal"></span>
                <form action="{{ url('login') }}" method="POST">
                    @csrf
                    <fieldset class="box-fieldset">
                        <label>Email<span>*</span>:</label>
                        <input type="text" name="login" class="form-contact style-1" placeholder="you@example.com" required>
                    </fieldset>

                    <fieldset class="box-fieldset">
                        <label>Password<span>*</span>:</label>
                        <div class="box-password">
                            <input type="password" name="password" class="form-contact style-1 password-field" placeholder="Password" required>
                            <span class="show-pass">
                                <i class="icon-pass icon-eye"></i>
                                <i class="icon-pass icon-eye-off"></i>
                            </span>
                        </div>
                    </fieldset>

                    <div class="d-flex justify-content-between flex-wrap gap-12">
                        <fieldset class="d-flex align-items-center gap-6">
                            <input type="checkbox" class="tf-checkbox style-2" id="remember_cb" name="remember" value="1">
                            <label for="remember_cb" class="caption-1 text-variant-1">Remember me</label>
                        </fieldset>
                        <a href="#" class="caption-1 text-primary">Forgot password?</a>
                    </div>

                    <div class="text-variant-1 auth-line">or sign up with</div>

                    <div class="login-social">
                        <a href="#" class="btn-login-social">
                            <img src="{{ asset('frontend/images/logo/fb.jpg') }}" alt="img">
                            Continue with Facebook
                        </a>
                        <a href="#" class="btn-login-social">
                            <img src="{{ asset('frontend/images/logo/google.jpg') }}" alt="img">
                            Continue with Google
                        </a>
                        <a href="#" class="btn-login-social">
                            <img src="{{ asset('frontend/images/logo/tw.jpg') }}" alt="img">
                            Continue with Twitter
                        </a>
                    </div>

                    <button type="submit" class="tf-btn primary w-100">Login</button>
                    <div class="mt-12 text-variant-1 text-center noti">
                        Not registered yet?
                        <a href="#modalRegister" data-bs-toggle="modal" class="text-black fw-5">Sign Up</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script src="{{ asset('frontend/js/jquery.min.js') }}"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('frontend/js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('frontend/js/carousel.js') }}"></script>
<script src="{{ asset('frontend/js/plugin.js') }}"></script>
<script src="{{ asset('frontend/js/jquery.nice-select.min.js') }}"></script>
<script src="{{ asset('frontend/js/animation_heading.js') }}"></script>
<script src="{{ asset('frontend/js/shortcodes.js') }}"></script>
<script src="{{ asset('frontend/js/main.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
  .vserve-launcher{
    position:fixed;
    right:18px;
    bottom:18px;
    z-index:9999;
    width:56px;
    height:56px;
    border-radius:999px;
    border:0;
    background:#ED2027;
    color:#fff;
    cursor:pointer;
    box-shadow:0 10px 30px rgba(0,0,0,.18);
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:800;
  }
  .vserve-launcher .vserve-badge{
    position:absolute;
    top:10px;
    right:10px;
    width:10px;
    height:10px;
    border-radius:999px;
    background:#ff2d55;
    box-shadow:0 0 0 3px rgba(255,255,255,.25);
    display:none;
  }
  .vserve-launcher.notify{ animation: vserveWiggle 1.2s ease-in-out infinite; }
  .vserve-launcher.notify .vserve-badge{ display:block; }

  @keyframes vserveWiggle{
    0%{ transform:translateY(0) scale(1); }
    15%{ transform:translateY(-4px) scale(1.03); }
    30%{ transform:translateY(0) scale(1); }
    45%{ transform:translateY(-3px) scale(1.02); }
    60%{ transform:translateY(0) scale(1); }
    100%{ transform:translateY(0) scale(1); }
  }

  .vserve-popup{
    position:fixed;
    right:18px;
    bottom:86px;
    z-index:999999;
    width:380px;
    max-width:calc(100vw - 24px);
    height:560px;
    max-height:calc(100vh - 120px);
    background:#fff;
    border:1px solid #e6e6e6;
    border-radius:16px;
    box-shadow:0 18px 60px rgba(0,0,0,.20);
    overflow:hidden;
    display:none;
  }
  .vserve-popup.open{ display:block; }
  .vserve-backdrop{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.18);
    z-index:999998;
    display:none;
  }
  .vserve-backdrop.open{ display:block; }
  .vserve-topbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    padding:12px 12px;
    background:#ED2027;
    color:#fff;
  }
  .vserve-title{display:flex;flex-direction:column;line-height:1.1;}
  .vserve-title b{font-size:14px;font-weight:bold;}
  .vserve-title span{font-size:12px;opacity:.85;}
  .vserve-actions{display:flex;gap:8px;}
  .vserve-iconbtn{
    background:rgba(255,255,255,.12);
    color:#fff;
    border:0;
    border-radius:10px;
    padding:8px 10px;
    cursor:pointer;
    font-weight:800;
    font-size:12px;
  }
  .vserve-body{
    height:calc(100% - 54px);
    display:flex;
    flex-direction:column;
    background:#f6f7f9;
  }
  .vserve-chat{
    flex:1;
    overflow:auto;
    padding:12px;
    scroll-behavior:auto;
  }
  .vserve-msg{
    max-width:86%;
    padding:10px 12px;
    border-radius:14px;
    margin:8px 0;
    line-height:1.35;
    font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial;
    font-size:14px;
    word-break:break-word;
  }
  .vserve-me{ margin-left:auto; background:#111; color:#fff; border-bottom-right-radius:6px; }
  .vserve-bot{ margin-right:auto; background:#fff; color:#111; border:1px solid #eee; border-bottom-left-radius:6px; }
  .vserve-name{ font-size:12px; opacity:.75; margin-bottom:6px; font-weight:800; }
  .vserve-row{
    display:flex;
    gap:8px;
    padding:10px;
    background:#fff;
    border-top:1px solid #eee;
  }
  .vserve-input{
    flex:1;
    padding:12px 12px;
    border:1px solid #ddd;
    border-radius:14px;
    background:#fff;
    outline:none;
  }
  .vserve-send{
    padding:12px 14px;
    border:0;
    border-radius:14px;
    cursor:pointer;
    background:#111;
    color:#fff;
    font-weight:800;
  }
  .vserve-divider{height:1px;background:#eee;margin:10px 0;}
  .vserve-smallbtn{background:#0f62fe;color:#fff;padding:10px 12px;border-radius:12px;font-size:13px;font-weight:800;border:0;cursor:pointer;}
  .vserve-ok{color:#0a7a2f;font-size:13px;}
  .vserve-err{color:#b00020;font-size:13px;}
  .vserve-leadCard input{
    width:100%;
    margin:6px 0;
    padding:10px 12px;
    border:1px solid #ddd;
    border-radius:12px;
    background:#fff;
    box-sizing:border-box;
  }
  @media (max-width:520px){
    .vserve-popup{
      right:12px;
      left:12px;
      width:auto;
      bottom:82px;
      height:70vh;
    }
    .vserve-launcher{
      right:12px;
      bottom:12px;
    }
    .vserve-msg{max-width:96%;}
  }
</style>

<div class="vserve-backdrop" id="vserveBackdrop" onclick="vserveToggle(false)"></div>

<button class="vserve-launcher" id="vserveLauncher" aria-label="Open chat" type="button" onclick="vserveToggle(true)">
  💬
  <span class="vserve-badge" aria-hidden="true"></span>
</button>

<div class="vserve-popup" id="vservePopup" role="dialog" aria-modal="true" aria-label="vServeAI chat">
  <div class="vserve-topbar">
    <div class="vserve-title">
      <b>vServeAI</b>
    </div>

    <div class="vserve-actions">
      <button class="vserve-iconbtn" type="button" onclick="vserveDownload()">Download</button>
      <button class="vserve-iconbtn" type="button" onclick="vserveReset()">Reset</button>
      <button class="vserve-iconbtn" type="button" onclick="vserveToggle(false)">✕</button>
    </div>
  </div>

  <div class="vserve-body">
    <div id="vserveChat" class="vserve-chat">
      <div class="vserve-msg vserve-bot" data-static="1">
        <div class="vserve-name">vServeAI</div>
        Welcome dear 👋 Tell me your <b>city/community</b> to start.<br>
        Examples: Dubai Marina, JVC, Dubai Hills, Delhi, Mumbai, Bangalore.
      </div>
    </div>

    <div class="vserve-row">
      <input id="vserveInput" class="vserve-input" type="text" placeholder="Type your requirement…" />
      <button class="vserve-send" type="button" onclick="vserveSend()">Send</button>
    </div>
  </div>
</div>

<script>
const VSERVE_CHAT_URL = @json(route('ai.chat.message', ['country' => $country]));
const VSERVE_LEAD_URL = @json(route('ai.chat.lead', ['country' => $country]));

const VS_UNREAD_KEY = 'vserveai_widget_unread';
const VS_CHAT_HTML_KEY = 'vserveai_widget_chat_html';
const VS_LEAD_KEY = 'vserveai_widget_lead_submitted';
const VS_OPEN_KEY = 'vserveai_widget_open';

let vserveLastIntent = null;
let vserveLastTopProjectId = null;
let vserveLastMessage = null;
let vserveIsSending = false;

function vserveGetCsrf(){
  const el = document.querySelector('meta[name="csrf-token"]');
  return el ? el.getAttribute('content') : '';
}
function vserveIsOpen(){
  return document.getElementById('vservePopup')?.classList.contains('open');
}
function vserveSetUnread(on){
  const launcher = document.getElementById('vserveLauncher');
  if(!launcher) return;
  if(on){
    launcher.classList.add('notify');
    sessionStorage.setItem(VS_UNREAD_KEY, '1');
  } else {
    launcher.classList.remove('notify');
    sessionStorage.setItem(VS_UNREAD_KEY, '0');
  }
}
(function(){ vserveSetUnread(sessionStorage.getItem(VS_UNREAD_KEY) === '1'); })();

function vserveToggle(open){
  const popup = document.getElementById('vservePopup');
  const backdrop = document.getElementById('vserveBackdrop');
  if(!popup || !backdrop) return;

  if(open){
    popup.classList.add('open');
    backdrop.classList.add('open');
    sessionStorage.setItem(VS_OPEN_KEY, '1');
    vserveSetUnread(false);
    setTimeout(() => document.getElementById('vserveInput')?.focus(), 50);
    vserveScrollBottom();
  } else {
    popup.classList.remove('open');
    backdrop.classList.remove('open');
    sessionStorage.setItem(VS_OPEN_KEY, '0');
  }
}
(function(){
  if(sessionStorage.getItem(VS_OPEN_KEY) === '1') vserveToggle(true);
})();

document.addEventListener('keydown', (e) => {
  if(e.key === 'Escape') vserveToggle(false);
});

function vserveSaveChat(){
  const chat = document.getElementById('vserveChat');
  if(chat) sessionStorage.setItem(VS_CHAT_HTML_KEY, chat.innerHTML);
}
function vserveRestoreChat(){
  const saved = sessionStorage.getItem(VS_CHAT_HTML_KEY);
  if(saved){
    const chat = document.getElementById('vserveChat');
    if(chat) chat.innerHTML = saved;
  }
}
function vserveScrollBottom(){
  const chat = document.getElementById('vserveChat');
  if(chat) chat.scrollTop = chat.scrollHeight;
}
vserveRestoreChat();

function vserveScrollToMsgStart(el){
  const chat = document.getElementById('vserveChat');
  if(!chat || !el) return;
  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      el.scrollIntoView({ block: 'start', inline: 'nearest' });
      chat.scrollTop = Math.max(0, chat.scrollTop - 8);
    });
  });
}

function vserveEscapeHtml(str){
  return String(str).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[s]));
}
function vserveFormatNum(n){
  try { return Number(n).toLocaleString(); } catch(e){ return n; }
}

function vserveAppendMsg(text, who='bot', extraHtml=null){
  const chat = document.getElementById('vserveChat');
  if(!chat) return null;

  const el = document.createElement('div');
  el.className = 'vserve-msg ' + (who === 'me' ? 'vserve-me' : 'vserve-bot');

  const safe = (text || '').replace(/\*\*(.*?)\*\*/g, '<b>$1</b>').replace(/\n/g, '<br>');

  if(who === 'bot'){
    el.innerHTML = `<div class="vserve-name">vServeAI</div>` + safe + (extraHtml ? extraHtml : '');
  } else {
    el.innerHTML = safe;
  }

  chat.appendChild(el);

  if(who === 'bot'){
    vserveScrollToMsgStart(el);
    if(!vserveIsOpen()) vserveSetUnread(true);
  } else {
    vserveScrollBottom();
  }

  vserveSaveChat();
  return el;
}

function vserveSetTyping(show=true){
  const chat = document.getElementById('vserveChat');
  if(!chat) return;
  const existing = document.getElementById('vserveTyping');

  if(show){
    if(existing) return;
    const el = document.createElement('div');
    el.id = 'vserveTyping';
    el.className = 'vserve-msg vserve-bot';
    el.innerHTML = `<div class="vserve-name">vServeAI</div>Typing…`;
    chat.appendChild(el);
    if(vserveIsOpen()) vserveScrollBottom();
    else vserveSetUnread(true);
  } else {
    if(existing) existing.remove();
  }
  vserveSaveChat();
}

function vserveCardsHtml(results){
  if(!results || !results.length) return '';
  let html = '<div class="vserve-divider"></div>';

  for(const r of results){
    const loc = r.location ? r.location : (r.city || '');
    const price = r.min_price ? `${vserveFormatNum(r.min_price)} ${vserveEscapeHtml(r.currency || '')}` : 'Price on request';

    html += `
      <div style="border:1px solid #eee; border-radius:14px; padding:12px; background:#fff; margin-top:10px;">
        <div style="font-weight:800; font-size:15px;">${vserveEscapeHtml(r.name || '')}</div>
        <div style="font-size:13px; color:#444; margin-top:4px;">
          ${vserveEscapeHtml(loc)}${r.city ? ' · ' + vserveEscapeHtml(r.city) : ''}
        </div>
        <div style="font-size:13px; color:#444; margin-top:6px;">
          <b>From:</b> ${price}
        </div>
        <div style="margin-top:10px;">
          <button class="vserve-smallbtn" type="button" onclick="vserveSelectProject(${r.project_id})">Enquire</button>
        </div>
      </div>
    `;
  }
  return html;
}

function vserveLeadCardHtml(){
  return `
    <div class="vserve-leadCard" style="margin-top:10px; border:1px dashed #ddd; padding:12px; border-radius:14px; background:#fbfbfb;">
      <div style="font-weight:800; margin-bottom:6px;">Want a call back?</div>
      <div style="font-size:13px; color:#666; margin-bottom:10px;">Share your contact — we’ll follow up with payment plan + availability.</div>
      <input class="leadName" type="text" placeholder="Name">
      <input class="leadPhone" type="text" placeholder="Phone">
      <input class="leadEmail" type="text" placeholder="Email (optional)">
      <button class="vserve-smallbtn" type="button" onclick="vserveSubmitLead(this)">Submit</button>
      <div class="leadStatus" style="margin-top:8px;"></div>
    </div>
  `;
}

function vserveLeadSubmitted(){ return sessionStorage.getItem(VS_LEAD_KEY) === '1'; }
function vserveHasLeadCard(){ return document.querySelector('.vserve-leadCard') !== null; }

function vserveShowLeadCard(){
  if(vserveLeadSubmitted()){
    vserveAppendMsg("Noted ✅ We already have your details. If you want to update, type “update details”.", "bot");
    return;
  }
  if(vserveHasLeadCard()){
    vserveAppendMsg("Perfect ✅ Please fill the callback form above and hit Submit.", "bot");
    return;
  }
  vserveAppendMsg("Great ✅ If you like, share your details and I’ll arrange the call back.", "bot", vserveLeadCardHtml());
}
function vserveSelectProject(projectId){
  vserveLastTopProjectId = projectId;
  vserveShowLeadCard();
}

async function vserveSafeJson(res){
  const ct = res.headers.get('content-type') || '';
  if(!ct.includes('application/json')){
    const txt = await res.text();
    return { __nonJson: true, text: txt };
  }
  return await res.json();
}

async function vserveSend(){
  if(vserveIsSending) return;

  const input = document.getElementById('vserveInput');
  const msg = (input.value || '').trim();
  if(!msg) return;

  if(msg.toLowerCase() === 'update details'){
    sessionStorage.removeItem(VS_LEAD_KEY);
    vserveAppendMsg("Sure ✅ Please share your updated contact details:", "bot", vserveLeadCardHtml());
    input.value = '';
    vserveSaveChat();
    return;
  }

  vserveLastMessage = msg;
  vserveAppendMsg(msg, 'me');
  input.value = '';

  vserveIsSending = true;
  vserveSetTyping(true);

  try{
    const res = await fetch(VSERVE_CHAT_URL, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': vserveGetCsrf(),
        'Accept': 'application/json'
      },
      body: JSON.stringify({ message: msg })
    });

    vserveSetTyping(false);

    if(!res.ok){
      const body = await res.text();
      console.error("Chat request failed", res.status, body);
      vserveAppendMsg(`Server error (${res.status}).`, 'bot');
      return;
    }

    const data = await vserveSafeJson(res);
    if(data.__nonJson){
      console.error("Non-JSON response:", data.text);
      vserveAppendMsg("Unexpected response. Check server logs.", 'bot');
      return;
    }

    if(!data.ok){
      vserveAppendMsg("Sorry dear — I couldn’t process that. Please try again.", 'bot');
      console.error("API ok=false:", data);
      return;
    }

    vserveLastIntent = data.intent || null;
    if(data.results && data.results.length) vserveLastTopProjectId = data.results[0].project_id;

    const extra = (data.show_cards && data.results && data.results.length) ? vserveCardsHtml(data.results) : '';
    vserveAppendMsg(data.reply || "Okay dear ✅", 'bot', extra);

    if(data.suggest_capture){
      vserveShowLeadCard();
    }
  }catch(e){
    vserveSetTyping(false);
    console.error("Chat fetch error:", e);
    vserveAppendMsg("Network error. Please try again.", 'bot');
  }finally{
    vserveIsSending = false;
    vserveSaveChat();
  }
}

async function vserveSubmitLead(btn){
  const card = btn.closest('.vserve-leadCard');
  const status = card.querySelector('.leadStatus');
  status.innerHTML = '';

  const name = (card.querySelector('.leadName').value || '').trim();
  const phone = (card.querySelector('.leadPhone').value || '').trim();
  const email = (card.querySelector('.leadEmail').value || '').trim();

  if(!name || !phone){
    status.innerHTML = `<div class="vserve-err">Name and phone are required.</div>`;
    return;
  }

  const filters = vserveLastIntent?.filters || null;

  const payload = {
    name,
    phone,
    email: email || null,
    project_id: vserveLastTopProjectId || null,
    preferred_bhk: filters?.bedrooms != null ? String(filters.bedrooms) : null,
    budget_max: filters?.max_price ?? null,
    property_type: filters?.property_type ?? null,
    property_build: filters?.timing === 'off_plan' ? 'off_plan' : (filters?.timing === 'ready' ? 'ready' : null),
    notes: `AI Chat (widget). Query: ${vserveLastMessage || ''}`
  };

  btn.disabled = true;
  btn.textContent = 'Submitting...';

  try{
    const res = await fetch(VSERVE_LEAD_URL, {
      method:'POST',
      credentials: 'same-origin',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': vserveGetCsrf(),
        'Accept':'application/json'
      },
      body: JSON.stringify(payload)
    });

    if(!res.ok){
      const body = await res.text();
      console.error("Lead submit failed", res.status, body);
      status.innerHTML = `<div class="vserve-err">Server error (${res.status}).</div>`;
      btn.disabled = false;
      btn.textContent = 'Submit';
      vserveSaveChat();
      return;
    }

    const data = await vserveSafeJson(res);
    if(data.ok){
      sessionStorage.setItem(VS_LEAD_KEY, '1');
      card.innerHTML = `
        <div class="vserve-ok" style="font-weight:800;">Submitted ✅</div>
        <div style="font-size:13px; color:#444; margin-top:6px;">
          Thanks ${vserveEscapeHtml(name)}! We’ll contact you shortly.
        </div>
      `;
      vserveAppendMsg("Done ✅ I’ve received your details. I’ll share payment plan + availability soon.", "bot");
    } else {
      status.innerHTML = `<div class="vserve-err">Failed to submit.</div>`;
      btn.disabled = false;
      btn.textContent = 'Submit';
    }
  }catch(e){
    console.error("Lead submit fetch error:", e);
    status.innerHTML = `<div class="vserve-err">Network error. Please try again.</div>`;
    btn.disabled = false;
    btn.textContent = 'Submit';
  }finally{
    vserveSaveChat();
  }
}

async function vserveReset(){
  vserveSetUnread(false);
  sessionStorage.removeItem(VS_UNREAD_KEY);
  sessionStorage.removeItem(VS_CHAT_HTML_KEY);
  sessionStorage.removeItem(VS_LEAD_KEY);

  vserveLastIntent = null;
  vserveLastTopProjectId = null;
  vserveLastMessage = null;

  const chat = document.getElementById('vserveChat');
  chat.innerHTML = `
    <div class="vserve-msg vserve-bot" data-static="1">
      <div class="vserve-name">vServeAI</div>
      Welcome dear 👋 Tell me your <b>city/community</b> to start.<br>
      Examples: Dubai Marina, JVC, Dubai Hills, Delhi, Mumbai, Bangalore.
    </div>
  `;
  vserveSaveChat();
  vserveScrollBottom();

  try{
    vserveSetTyping(true);
    const res = await fetch(VSERVE_CHAT_URL, {
      method:'POST',
      credentials: 'same-origin',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': vserveGetCsrf(),
        'Accept':'application/json'
      },
      body: JSON.stringify({message: "reset"})
    });
    vserveSetTyping(false);

    if(res.ok){
      const data = await vserveSafeJson(res);
      if(data?.ok && data?.reply){
        vserveAppendMsg(data.reply, 'bot');
      }
    }
  }catch(e){
    vserveSetTyping(false);
    console.error("Reset fetch error:", e);
  }
}

function vserveDownload(){
  const msgs = Array.from(document.querySelectorAll('#vserveChat .vserve-msg'));
  const lines = msgs.map(el => {
    const who = el.classList.contains('vserve-me') ? 'You' : 'vServeAI';
    const text = (el.textContent || '').replace(/\s+\n/g, '\n').trim();
    return `${who}: ${text}`;
  });

  const content = lines.join('\n\n');
  const blob = new Blob([content], {type:'text/plain;charset=utf-8'});
  const url = URL.createObjectURL(blob);

  const a = document.createElement('a');
  a.href = url;
  a.download = `vserveai-chat-${new Date().toISOString().slice(0,10)}.txt`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
}

document.getElementById('vserveInput').addEventListener('keydown', (e) => {
  if(e.key === 'Enter'){
    e.preventDefault();
    vserveSend();
  }
});
</script>
<script>
function changeCountry(country) {
    let setUrl = "{{ route('set.country', ':country') }}";
    setUrl = setUrl.replace(':country', country);
    fetch(setUrl).then(() => { 
        let redirectUrl = "{{ url(':country') }}";
        redirectUrl = redirectUrl.replace(':country', country);
        window.location.href = redirectUrl;
    });

}
</script>
<script>
function toggleCountryMenu() {
    let menu = document.getElementById("countryDropdown");

    if (menu.style.display === "block") {
        menu.style.display = "none";
    } else {
        menu.style.display = "block";
    }
}

// click outside = close
document.addEventListener("click", function(event) {
    let dropdown = document.getElementById("countryDropdown");
    let globe = document.querySelector(".mobile-globe");

    if (!globe.contains(event.target)) {
        dropdown.style.display = "none";
    }
});
</script>

@yield('scripts')

</body>
</html>
