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

        .flat-map {
            padding: 110px 0 0px;
            background: #ffffff;
        }

        #container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 16px;
            text-align: center;
        }

        .hero-title {
            margin: 0 0 50px;
            font-family: Georgia, "Times New Roman", serif;
            font-weight: 800;
            color: #0b2a4a;
            font-size: clamp(34px, 4.2vw, 64px);
            line-height: 1.05;
        }

        .hero-title span {
            display: inline-block;
        }

        .hero-search {
            display: flex;
            justify-content: center;
            align-items: stretch;
            gap: 18px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .hero-search input {
            width: min(720px, 90vw);
            height: 64px;
            padding: 0 22px;
            font-size: 20px;
            border: 1px solid #e7e7e7;
            border-radius: 6px;
            outline: none;
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.02);
        }

        .hero-search input:focus {
            border-color: #cfcfcf;
        }

        .hero-search button {
            height: 64px;
            padding: 0 28px;
            font-size: 20px;
            font-weight: 700;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            background: #ed2027;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .hero-search button:hover {
            filter: brightness(0.95);
        }

        .hero-search .icon {
            font-size: 18px;
            line-height: 1;
        }

        @media (max-width: 640px) {
            .hero-search {
                gap: 12px;
            }

            .hero-search input,
            .hero-search button {
                width: 100%;
            }
        }

        .images-style {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
        }

        .images-style img {
            width: 100%;
            height: 260px;
            object-fit: cover;
            display: block;
        }

        .developer-logo {
            position: absolute;
            bottom: 14px;
            left: 30%;
            transform: translateX(-50%);
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 70%;
        }

        .developer-logo img {
            max-height: 60px;
            width: auto;
            object-fit: contain;
        }

        .homeya-box {
            border: none;
        }

        .homeya-box .content {
            border-bottom: none;
        }
        .image-cap {
            width: 100%;
            height: 100%;
            background: #6c757d; /* grey */
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 500;
        }
    </style>
    <section class="flat-map">
        <div class="container" id="container">
            <h1 class="hero-title">
                Trusted Property Agents in  {{ $countries->country_name == 'India' ? 'India' : 'UAE' }} <br />
                {{-- <span>&amp; The UAE</span> --}}
            </h1>
            <form class="hero-search" action="{{ route('all_agents') }}" method="get">
                <input type="text" name="search_deve" placeholder="Agent Name" aria-label="Search Agent"
                    value="{{ request('search_deve') }}" />
                <button type="submit">
                    Search
                    <span class="icon" aria-hidden="true">⌕</span>
                </button>
            </form>
        </div>
    </section>

    <section class="flat-section-v5 flat-recommended flat-recommended-v2">
        <div class="container">
            <div class="box-title-listing">
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
                        @foreach ($data as $p)
                            <div class="box col-lg-4 col-sm-6">
                                <div class="box-agent style-1 hover-img"> 
                                    <a href="#" class="box-img img-style agent-img">
                                        <img src="{{ asset('storage/'.$p->image) }}" alt="image-agent">
                                        <ul class="agent-social">
                                            <li>
                                                <span class="icon icon-facebook"
                                                    onclick="window.open('{{ $p->facebook }}','_blank')"
                                                    style="cursor:pointer;"></span>
                                            </li>
                                            <li>
                                                <span class="icon icon-linkedin"
                                                    onclick="window.open('{{ $p->linkedin }}','_blank')"
                                                    style="cursor:pointer;"></span>
                                            </li>
                                            <li>
                                                <span class="icon icon-twitter"
                                                    onclick="window.open('{{ $p->twitter }}','_blank')"
                                                    style="cursor:pointer;"></span>
                                            </li>
                                            <li>
                                                <span class="icon icon-instagram"
                                                    onclick="window.open('{{ $p->instagram }}','_blank')"
                                                    style="cursor:pointer;"></span>
                                            </li>
                                        </ul>
                                    </a>
                                    <a href="#" class="content">
                                        <div class="info">
                                            <h6 class="link"> {{ ucwords($p->name)}}</h6>
                                            <p class="mt-4 text-variant-1">{{ ucwords($p->desgination)}}</p>
                                        </div>
                                        <span class="icon-phone"></span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $data->onEachSide(1)->links('pagination.wd-navigation') }}
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
@section('scripts')
@endsection
