@extends('layouts.frontend.frontend_layout')

@section('main-content')
    <style>
        .services-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('{{ asset('frontend/images/banner/banner-service.jpeg') }}');
            background-size: cover;
            background-position: center;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .services-container {
            max-width: 1296px;
            margin: 60px auto;
            padding: 0 20px;
        }


        .service-row {
            display: flex;
            align-items: center;
            gap: 60px;
            margin-bottom: 30px;
            padding: 40px 30px;
            border-radius: 20px;
            transition: all 0.4s ease;
        }

        .service-row:hover {
            background-color: #FFF8F1;
            transform: translateY(-5px);
        }

        .services-container {
            max-width: 1296px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .service-row:nth-child(even) {
            flex-direction: row-reverse;
        }

        .service-content {
            flex: 1;
        }

        .service-image {
            flex: 1;
        }

        .service-image img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .service-tag {
            color: #e31e24;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            display: block;
        }

        .service-content h2 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1a1a1a;
        }

        .service-content p {
            color: #666;
            line-height: 1.7;
            font-size: 14.5px;
        }

        /* Responsive */
        @media (max-width: 768px) {

            .service-row,
            .service-row:nth-child(even) {
                flex-direction: column;
                text-align: center;
                gap: 20px;
                margin-bottom: 50px;
            }

            .service-image img {
                height: 200px;
            }
        }
    </style>

    <div class="services-hero">
        <h1 class="hero-title" style="color: #fff; font-weight: 500">Services</h1>
    </div>

    <section class="services-container">

        <div class="service-row">
            <div class="service-content">
                <span class="service-tag">Professional Property Management</span>
                <h2>Property Management</h2>
                <p>
                    Vserve manages every aspect of your property with precision. From maintenance & tenant relations to rent
                    collection & compliance, our team ensures smooth operations while optimizing returns. Advanced tools &
                    expert oversight keep your property performing at its best.
                </p>

            </div>
            <div class="service-image">
                <img src="{{ asset('frontend/images/services/s1.png') }}" alt="Property Management">
            </div>
        </div>

        <div class="service-row">
            <div class="service-content">
                <span class="service-tag">Showcase Your Property Effectively</span>
                <h2>List Your Property</h2>
                <p>A strong listing attracts serious buyers or tenants quickly. Vserve crafts professional listings with
                    high-quality visuals, precise descriptions & targeted marketing strategies. Every property is positioned
                    to reach the right audience & generate results efficiently.</p>
            </div>
            <div class="service-image">
                <img src="{{ asset('frontend/images/services/s2.png') }}" alt="List Your Property">
            </div>
        </div>

        <div class="service-row">
            <div class="service-content">
                <span class="service-tag">Simplified Mortgage Solutions</span>
                <h2>Mortgages</h2>
                <p>Navigating mortgage options can be complex. Vserve connects you with suitable financing solutions &
                    guides you through every step, from pre-approval to disbursement. Our goal is to make the process
                    transparent, helping you secure the most appropriate financial plan for your property needs.</p>
            </div>
            <div class="service-image">
                <img src="{{ asset('frontend/images/services/s3.png') }}" alt="Mortgages">
            </div>
        </div>

        <div class="service-row">
            <div class="service-content">
                <span class="service-tag">Secure & Transparent Property Transfers</span>
                <h2>Conveyancing</h2>
                <p>Vserve provides expert conveyancing services, managing documentation, title verification & registration
                    accurately. Our process is designed to minimize risk & ensure clarity at every stage, allowing property
                    transfers to proceed seamlessly.</p>
            </div>
            <div class="service-image">
                <img src="{{ asset('frontend/images/services/s4.png') }}" alt="Conveyancing">
            </div>
        </div>
        <div class="service-row">
            <div class="service-content">
                <span class="service-tag">Maximize Rental Potential</span>
                <h2>Short-Term Rentals</h2>
                <p>Vserve manages short-term rental properties efficiently, handling bookings, guest communications & upkeep
                    while optimizing pricing & occupancy. Our approach ensures your property generates consistent income
                    with professional management standards.</p>
            </div>
            <div class="service-image">
                <img src="{{ asset('frontend/images/services/s5.png') }}" alt="Conveyancing">
            </div>
        </div>
        <div class="service-row">
            <div class="service-content">
                <span class="service-tag">Strategic Land Investment</span>
                <h2>Plots</h2>
                <p>Vserve identifies high-potential plots, evaluates development opportunities & guides you through
                    approvals & legal processes. Every recommendation is backed by market insight, ensuring your land
                    investment is both informed & strategic.</p>
            </div>
            <div class="service-image">
                <img src="{{ asset('frontend/images/services/s6.png') }}" alt="Conveyancing">
            </div>
        </div>
        <div class="service-row">
            <div class="service-content">
                <span class="service-tag">Expand with Vserve Partners</span>
                <h2>Partner Program</h2>
                <p>Our Partner Program connects agents, investors & developers to exclusive opportunities & marketing
                    support. Collaborate with Vserve to grow your network, access insights & expand business opportunities
                    in the real estate market.</p>
            </div>
            <div class="service-image">
                <img src="{{ asset('frontend/images/services/s7.png') }}" alt="Conveyancing">
            </div>
        </div>

    </section>
@endsection
