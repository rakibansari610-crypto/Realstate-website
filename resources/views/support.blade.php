<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Asset X | Support</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: radial-gradient(1200px 600px at 20% -10%, #e3f4fc 0%, #fff 45%, #fff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-dot {
            width: 12px;
            height: 12px;
            background: #326ba8;
            border-radius: 50%;
            display: inline-block;
        }

        .support-card {
            max-width: 520px;
            width: 100%;
            border-radius: 14px;
            border: 1px solid #dee5ec;
        }

        .support-card .form-control {
            padding: 12px 14px;
            font-size: 14px;
        }

        .support-card textarea {
            resize: none;
        }

        .btn-asset {
            background: #212529;
            color: #fff;
            padding: 10px;
            font-size: 15px;
        }

        .btn-asset:hover {
            background: #000;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: radial-gradient(1200px 600px at 20% -10%, #13344a 0%, #0b2238 60%, #0b2238 100%);
                color: #e3e4e6;
            }

            .support-card {
                background: #17334b;
                border-color: #33597d;
            }

            .form-control {
                background: #0b2238;
                border-color: #33597d;
                color: #e3e4e6;
            }

            .form-control::placeholder {
                color: #9fb3c8;
            }
        }
    </style>
</head>

<body>

    <div class="card support-card shadow-sm p-4">
        <form action="{{ route('support-store') }}" method="POST" >
            @csrf

            @if (session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger text-center">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Header -->
            <div class="d-flex justify-content-center align-items-center gap-3 mb-4">
                <span class="brand-dot"></span>
                <h4 class="mb-0 fw-bold">Asset X Support</h4>
                {{-- <img height="40" src="{{ url('assets/images/logo/logo.png') }}" alt="Asset X"> --}}
            </div>

            <!-- Form Fields -->
            <div class="mb-3">
                <input type="text" name="name" class="form-control" placeholder="Your Name" required>
            </div>

            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email Address" required>
            </div>

            <div class="mb-3">
                <input type="tel" name="phone_no" class="form-control" placeholder="Phone Number" required>
            </div>

            <div class="mb-4">
                <textarea name="message" class="form-control" rows="4" placeholder="How can we help you?"
                    required></textarea>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-asset w-100">
                Submit Request
            </button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
