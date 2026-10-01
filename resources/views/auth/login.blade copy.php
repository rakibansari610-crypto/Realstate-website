<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-layout-mode="light" data-body-image="img-1" data-preloader="disable">
@php
    $uiSetting = []; //getUISetting();
    $brand = $brand ?? [];
    $orgName = $currentOrg->name ?? config('app.name', 'Visitor Suite');

    // brand colors
    $primary = data_get($brand, 'colors.primary', '#4F46E5'); // indigo-600
    $header = data_get($brand, 'colors.header', '#FFFFFF');
    $loginBg = data_get($brand, 'colors.login_bg', '#1e2a78'); // deep blue
    $btnColor = data_get($brand, 'colors.button', '#7C3AED'); // violet-600
@endphp

<head>
    <meta charset="utf-8" />
    <title>Sign In | {{ $orgName }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('asset/images/favicon.ico') }}">

    <!-- Core CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" />

    <style>
        :root {
            --vs-primary: {{ $primary }};
            --vs-header: {{ $header }};
            --vs-loginbg: {{ $loginBg }};
            --vs-button: {{ $btnColor }};
            --vs-bg1: #2b2f77;
            /* gradient stops to mirror reception */
            --vs-bg2: #223061;
            --vs-bg3: #162c58;
            --vs-bg4: #112a52;
        }

        /* Full-screen gradient like reception */
        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e9eefb;

            /* If org background image exists */
            @if (!empty($currentOrg) && $currentOrg->login_bg_path)
                background-image: url('{{ asset('storage/' . $currentOrg->login_bg_path) }}') !important;
                background-repeat: no-repeat !important;
                background-position: center center !important;
                background-size: cover !important;
                /* <-- full-screen cover */
                background-attachment: fixed;
                /* optional: nice parallax on tall pages */
                background-color: #0b1123;
                /* fallback color while image loads */
            @else
                background:
                    radial-gradient(1200px 600px at 85% -10%, rgba(99, 102, 241, .35), transparent 60%),
                    radial-gradient(900px 500px at 10% -20%, rgba(56, 189, 248, .30), transparent 60%),
                    linear-gradient(135deg, var(--vs-bg1) 0%, var(--vs-bg2) 35%, var(--vs-bg3) 60%, var(--vs-bg4) 100%);
            @endif
            overflow:hidden;
        }

        /* Optional: add a subtle dark scrim to ensure foreground readability on bright images */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, .28), rgba(0, 0, 0, .28));
            pointer-events: none;
        }

        /* Login wrapper */
        .vs-login {
            position: relative;
            width: 100%;
            max-width: 1120px;
            padding: 24px;
        }

        .vs-login-card {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 0;
            background: rgba(17, 24, 39, .45);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 24px;
            box-shadow: 0 18px 48px rgba(0, 0, 0, .45);
            overflow: hidden;
            backdrop-filter: blur(12px);
        }

        /* Left: welcome panel (glass) */
        .vs-login-hero {
            position: relative;
            padding: 42px 38px;
            min-height: 420px;
            background: linear-gradient(160deg, rgba(124, 58, 237, .22), rgba(14, 165, 233, .18));
            border-right: 1px solid rgba(255, 255, 255, .08);
        }

        .vs-hero-title {
            margin: 0 0 8px;
            font-weight: 900;
            font-size: 34px;
            color: #fff;
            text-shadow: 0 2px 12px rgba(0, 0, 0, .25);
        }

        .vs-hero-sub {
            color: #cfe4ff;
            font-size: 16px;
            margin-bottom: 18px;
        }

        .vs-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .vs-brand img {
            height: 56px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .06);
            padding: 6px;
        }

        .vs-org {
            font-size: 20px;
            font-weight: 800;
            color: #fff;
        }

        /* Right: form */
        .vs-login-form {
            padding: 42px 38px;
            background: rgba(11, 17, 35, .35);
        }

        .vs-form-title {
            margin: 0 0 18px;
            font-weight: 800;
            color: #eaf1ff;
            font-size: 22px;
        }

        .form-label {
            color: #e9efff;
            font-weight: 600;
        }

        .form-control {
            color: #fff;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 12px;
            padding: 10px 12px;
        }

        .form-control:focus {
            color: #fff;
            background: rgba(255, 255, 255, .12);
            border-color: #93c5fd;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, .25);
        }

        .btn-primary {
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--vs-primary), var(--vs-button)) !important;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .35);
            font-weight: 800;
        }

        .btn-primary:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
        }

        .vs-meta {
            margin-top: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            color: #c7cfe6;
            font-size: .92rem;
        }

        .alert {
            border-radius: 12px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .vs-login-card {
                grid-template-columns: 1fr;
            }

            .vs-login-hero {
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, .08);
            }
        }

        .vs-login-footer {
            color: #cfd8ff;
            background: rgba(11, 17, 35, 0.85);
            /* semi-opaque dark, looks like dashboard */
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(6px);
            /* optional glassy feel */
            display: inline-block;
            /* shrink to content */
            padding: 10px 16px;
            border-radius: 12px;
            box-shadow: 0 8px 22px rgba(0, 0, 0, .35);
        }

      
.vs-login-footer{
  width:100%;
  color:#eaf1ff;
  background:#0b1123;               // solid
  padding: 12px 18px;
  border-top: 1px solid rgba(255,255,255,0.08);
  border-radius: 14px;
}

    </style>
</head>

<body>
    <div class="vs-login">
        <div class="vs-login-card">
            <!-- Left: Hero / matches reception vibe -->
            <div class="vs-login-hero">
                <div class="vs-brand">
                    @if (!empty($currentOrg) && $currentOrg->logo_path)
                        <img src="{{ asset('storage/' . $currentOrg->logo_path) }}" alt="{{ $orgName }}">
                    @endif
                    <div class="vs-org">{{ $orgName }}</div>
                </div>

                <h1 class="vs-hero-title">
                    Good
                    {{ now()->format('H') < 12 ? 'Morning' : (now()->format('H') < 18 ? 'Afternoon' : 'Evening') }}
                    <span>👋</span>
                </h1>
                <p class="vs-hero-sub">
                    Welcome to Visitor Suite. Please sign in to manage visits, approvals, and guest registrations.
                </p>

                {{-- Optional decorative SVG wave (subtle) --}}
                <svg width="100%" height="110" viewBox="0 0 800 200" style="opacity:.18; margin-top:auto;">
                    <path d="M0,150 C150,90 250,170 400,110 C550,60 650,120 800,80 L800,200 L0,200 Z" fill="#8B5CF6" />
                </svg>
            </div>

            <!-- Right: Form panel -->
            <div class="vs-login-form">
                <h2 class="vs-form-title">Sign in to your account</h2>

                <form action="{{ url('login') }}" method="POST" class="mt-3">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input id="login" type="text" name="login" class="form-control"
                            placeholder="you@example.com" required autofocus>
                    </div>

                    <div class="mb-2">
                        <label for="password-input" class="form-label">Password</label>
                        <div class="position-relative">
                            <input id="password-input" name="password" type="password" class="form-control pe-5"
                                placeholder="Enter password" required>
                            <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted"
                                type="button" id="password-addon" aria-label="toggle password">
                                <i class="ri-eye-fill align-middle"></i>
                            </button>
                        </div>
                    </div>

                    <div class="vs-meta">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="remember"
                                name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <a href=""
                            class="link-light text-decoration-underline">Forgot password?</a>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-primary w-100" type="submit">Sign In</button>
                    </div>

                    @if (session('error'))
                        <div class="alert alert-danger text-white bg-danger mt-3">{{ session('error') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger text-white bg-danger mt-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="vs-login-footer text-center mt-3">
            &copy;
            <script>
                document.write(new Date().getFullYear())
            </script>
            Visitor Suite • Powered by KGN INFOTECH LLP
        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script>
        // password show/hide
        (function() {
            const btn = document.getElementById('password-addon');
            const input = document.getElementById('password-input');
            if (!btn || !input) return;
            btn.addEventListener('click', () => {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                btn.querySelector('i')?.classList.toggle('ri-eye-off-fill');
            });
        })();
    </script>
</body>

</html>
