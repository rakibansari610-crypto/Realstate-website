@extends('layouts.frontend.frontend_layout')

@section('main-content')
    <style>
        .contact-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset('frontend/images/banner/contact-us-banner.jpg') }}');
            /* Replace with your image */
            background-size: cover;
            background-position: center;
            height: 450px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .contact-wrapper {
            max-width: 1296px;
            margin: -120px auto 80px;
            /* Overlap effect */
            display: flex;
            background: white;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            position: relative;
            z-index: 10;
        }

        .form-side {
            flex: 2;
            padding: 50px;
        }

        .info-side {
            flex: 1;
            background: #000;
            color: white;
            padding: 50px;
        }

        .input-group {
            margin-bottom: 25px;
        }

        .input-group label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
            color: #333;
        }

        .input-group input,
        .input-group textarea {
            width: 100%;
            border: none;
            border-bottom: 1px solid #ccc;
            padding: 8px 0;
            outline: none;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .input-group input:focus {
            border-bottom: 1px solid #e31e24;
            /* Red accent */
        }

        .info-title {
            font-size: 32px;
            line-height: 1.2;
            margin-bottom: 40px;
            font-weight: 400;
            color: #fff;
        }

        .contact-detail-item {
            margin-bottom: 30px;
        }

        .detail-label {
            color: #888;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
            display: block;
        }

        .detail-text {
            font-size: 14px;
            line-height: 1.6;
        }

        .submit-btn {
            background: #e31e24;
            color: white;
            padding: 12px 30px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            margin-top: 10px;
        }

        @media (max-width: 992px) {
            .contact-wrapper {
                flex-direction: column;
                margin-top: 20px;
            }

            .info-side {
                order: -1;
            }
        }
    </style>

    <div class="contact-hero">
        <div>
            <h1 class="hero-title" style="color: #fff; font-weight: 500">Talk To Us</h1>
            <p style="font-size: 16px; opacity: 0.9;">
                Looking to explore property opportunities or join the Vserve One network?
            </p>
        </div>
    </div>

    <div class="contact-wrapper">
        <div class="form-side">
            <h2 style="font-size: 36px; margin-bottom: 10px; font-weight: 500;">Share Your Requirement</h2>
            <p style="color: #666; margin-bottom: 40px; font-size: 14px;">Let us know the location, purpose, and timeline.
                We'll come back with clear direction and relevant options.</p>

            <form action="{{route('contact-us-enquiry-store')}}" method="post" >
                @csrf
                <div class="input-group">
                    <label>Name</label>
                    <input type="text" name="name" placeholder="Enter Your Name">
                    @error('name')
                        <span style="color:red;font-size:13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: flex; gap: 30px;">
                    <div class="input-group" style="flex: 1;">
                        <label>Phone No.</label>
                        <input type="number"  name="phone_no" placeholder="Enter Phone No.">
                        @error('phone_no')
                            <span style="color:red;font-size:13px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="input-group" style="flex: 1;">
                        <label>E-mail</label>
                        <input type="email" name="email" placeholder="Enter E-mail">
                        @error('email')
                            <span style="color:red;font-size:13px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="display: flex; gap: 30px;">
                    <div class="input-group" style="flex: 1;">
                        <label>Timeline</label>
                        <input type="text" name="timeline" placeholder="Enter Your Timeline">
                        @error('timeline')
                            <span style="color:red;font-size:13px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="input-group" style="flex: 1;">
                        <label>Location</label>
                        <input type="text" name="location" placeholder="Enter Your location">
                        @error('location')
                            <span style="color:red;font-size:13px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="input-group">
                    <label>Message (Purpose)</label>
                    <textarea rows="2" name="message" placeholder="Enter Your Message"></textarea>
                    @error('message')
                        <span style="color:red;font-size:13px;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="submit-btn">Get In Touch</button>
                @if(session('success'))
                    <div style="color:green; margin-top:15px; font-size:14px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div style="color:red; margin-top:15px; font-size:14px;">
                        {{ session('error') }}
                    </div>
                @endif
            </form>
        </div>

        <div class="info-side">
            <h2 class="info-title">Clear Advice.<br>Thoughtful Guidance.</h2>
            <hr style="border: 0; border-top: 1px solid #fff; margin-bottom: 30px;">

            <div class="contact-detail-item">
                <span class="detail-label">REACH US</span>
                <div class="detail-text">
                    <p><i class="fas fa-phone" style="color: #e31e24; margin-right: 8px;"></i> +91-9211331048</p>
                    <p><i class="fas fa-phone" style="color: #e31e24; margin-right: 8px;"></i> +971 58 586 2033</p>
                    <p>
                        <i class="fas fa-envelope" style="color: #e31e24; margin-right: 8px;"></i>
                        info@vserverealestate.com
                    </p>
                </div>
            </div>

            <div class="contact-detail-item">
                <span class="detail-label">LOCATION</span>
                <p style="font-weight: 600; margin-top: 15px; margin-bottom: 5px;">
                    <i class="fas fa-map-marker-alt" style="color: #e31e24; margin-right: 8px;"></i> Dubai Address
                </p>
                <p class="detail-text" style="color: #bbb;">2802, Single Business Tower Business Bay – Dubai United Arab
                    Emirates</p>
            </div>

            <div class="contact-detail-item">
                <p style="font-weight: 600; margin-bottom: 5px;">
                    <i class="fas fa-map-marker-alt" style="color: #e31e24; margin-right: 8px;"></i> India Address
                </p>
                <p class="detail-text" style="color: #bbb;">A-167, A Block, Sector 63 Noida, Uttar Pradesh 201301</p>
            </div>
        </div>
    </div>
@endsection
