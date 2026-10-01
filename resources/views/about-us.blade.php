@extends('layouts.frontend.frontend_layout')

@section('main-content')
    <style>
        .about-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
             url('{{ asset('frontend/images/banner/about-us-banner.jpg') }}');
            background-size: cover;
            background-position: center;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .intro-section {
            max-width: 1296px;
            margin: 80px auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            gap: 50px;
        }

        .intro-content {
            flex: 1;
        }

        .intro-image {
            flex: 1;
            text-align: right;
        }

        .intro-image img {
            width: 100%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .tagline {
            color: #e31e24;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .statement-container {
            max-width: 1296px;
            margin: 0 auto 100px;
            padding: 0 20px;
        }

        .statement-card {
            background: #000;
            color: white;
            border-radius: 20px;
            padding: 40px 60px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 40px;
            height: 312px;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            flex-direction: row;
        }

        .card-label {
            color: #e31e24;
        }

        .statement-card:hover {
            flex-direction: row-reverse;
            background: #e31e24;
        }

        .statement-card:hover .card-label,
        .statement-card:hover p,
        .statement-card:hover span {
            color: #fff;
        }

        .statement-img-box,
        .statement-text {
            transition: all 0.6s ease;
            pointer-events: none;
        }

        .statement-card:hover .statement-img-box,
        .statement-card:hover .statement-text {
            animation: fadeIn 0.6s forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0.5;
                transform: translateX(10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .statement-img-box {
            flex: 1;
        }

        .statement-text {
            flex: 1.5;
        }

        .statement-img-box img {
            width: 100%;
            height: 207px;
            object-fit: cover;
            border-radius: 15px;
        }

        .row-reverse-default {
            flex-direction: row-reverse;
        }

        .row-reverse-default:hover {
            flex-direction: row;
        }

        @media (max-width: 992px) {
            .intro-section {
                flex-direction: column;
                text-align: center;
                gap: 30px;
                margin: 40px auto;
            }

            .intro-image {
                text-align: center;
                order: -1;
            }

            .statement-card {
                flex-direction: column !important;
                height: auto;
                padding: 30px 20px;
                gap: 20px;
                text-align: center;
            }

            .statement-img-box img {
                height: 200px;
                width: 100%;
            }

            .statement-text {
                flex: unset;
            }

            .statement-card:hover {
                flex-direction: column !important;
                transform: translateY(-5px);
            }

            .statement-card:hover .statement-img-box,
            .statement-card:hover .statement-text {
                animation: none;
                transform: none;
            }
        }

        @media (max-width: 576px) {
            .about-hero h1 {
                font-size: 32px;
            }

            .intro-content h2 {
                font-size: 28px !important;
            }

            .statement-card h3 {
                font-size: 24px !important;
            }
        }
    </style>

    <div class="about-hero">
        <h1 class="hero-title" style="color: #fff; font-weight: 500">About Us</h1>
    </div>

    <section class="intro-section">
        <div class="intro-content">
            <span class="tagline">Brand Introduction / Who We Are</span>
            <h2 style="font-size: 38px; margin: 15px 0; font-weight: 600;">Redefining Real Estate, Your Way</h2>
            <p style="color: #666; line-height: 1.8; margin-bottom: 20px; font-size: 15px;">
                At Vserve Real Estate, we see every property as more than a space—it’s an opportunity, an investment, a
                milestone. Our goal is to make your property journey clear, practical & tailored to you. We don’t just show
                options; we listen, understand your priorities & guide you to the right decisions.
            </p>
            <p style="color: #666; line-height: 1.8; font-size: 15px;">
                With deep market insight, modern tools & a personal approach, we help clients navigate property management,
                investments, rentals & more—without confusion or unnecessary complexity. At Vserve, you gain more than a
                service provider—you gain a partner who cares about your goals as much as you do.
            </p>
        </div>
        <div class="intro-image">
            <img src="{{ asset('frontend/images/about-us/about_1.png') }}"
                alt="Mobile App Vserve">
        </div>
    </section>

    {{-- <section class="statement-container">
    
    <div class="statement-card">
        <div class="statement-img-box">
            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=500&auto=format&fit=crop" alt="Mission Image">
        </div>
        <div class="statement-text">
            <span class="card-label">Our Mission</span>
            <h3 style="font-size: 32px; margin-bottom: 15px; font-weight: 400;color: #fff;">Mission Statement</h3>
            <p style="font-size: 14px; color: #fff; line-height: 1.6;">
                Our mission is to guide every client through property decisions with clarity & confidence. We start by listening, understanding your priorities & provide solutions that make sense for your goals. At Vserve, real estate is more than transactions—it’s about creating outcomes you can trust & rely on.
            </p>
        </div>
    </div>

    <div class="statement-card">
        <div class="statement-img-box">
            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=500&auto=format&fit=crop" alt="Vision Image">
        </div>
        <div class="statement-text">
            <span class="card-label">Our Vision</span>
            <h3 style="font-size: 32px; margin-bottom: 15px; font-weight: 400; color: #fff;">Vision Statement</h3>
            <p style="font-size: 14px; color: #fff; line-height: 1.6;">
                Our vision is to make property decisions effortless & meaningful for everyone we work with. We aim to be the real estate partner people trust to guide them—whether it’s managing a property, finding an investment, or securing the perfect rental. In every interaction, we want clients to feel informed, confident & supported.
            </p>
        </div>
    </div>

</section> --}}
    <section class="statement-container">

        <div class="statement-card">
            <div class="statement-img-box">
                <img src="{{ asset('frontend/images/banner/about-us-banner.jpg') }}"
                    alt="Mission Image">
            </div>
            <div class="statement-text">
                <span class="card-label">Our Mission</span>
                <h3 style="font-size: 32px; margin-bottom: 15px; font-weight: 400;color: #fff;">Mission Statement</h3>
                <p style="font-size: 14px; color: #fff; line-height: 1.6;">
                    Our mission is to guide every client through property decisions with clarity & confidence. We start by
                    listening, understanding your priorities & provide solutions that make sense for your goals. At Vserve,
                    real estate is more than transactions—it’s about creating outcomes you can trust & rely on.
                </p>
            </div>
        </div>

        <div class="statement-card row-reverse-default">
            <div class="statement-img-box">
                <img src="{{ asset('frontend/images/about-us/about_2.jpg') }}"
                    alt="Vision Image">
            </div>
            <div class="statement-text">
                <span class="card-label">Our Vision</span>
                <h3 style="font-size: 32px; margin-bottom: 15px; font-weight: 400; color: #fff;">Vision Statement</h3>
                <p style="font-size: 14px; color: #fff; line-height: 1.6;">
                    Our vision is to make property decisions effortless & meaningful for everyone we work with. We aim to be
                    the real estate partner people trust to guide them—whether it’s managing a property, finding an
                    investment, or securing the perfect rental. In every interaction, we want clients to feel informed,
                    confident & supported.
                </p>
            </div>
        </div>

    </section>
@endsection
