<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Asset X – Real Estate Partner Suite</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    .hero-wrap {
      position: relative;
      background: radial-gradient(1200px 600px at 20% -10%, #e3f4fc 0%, #fff 45%, #fff 100%);
    }
    @media (prefers-color-scheme: dark) {
      body { color: #e3e4e6; background-color: #0b2238; }
      .navbar, .card, .cta {
        --bs-body-bg: #17334b; --bs-body-color: #e3e4e6; background-color: #17334b; color: #e3e4e6;
        border-color: #33597d;
      }
      .hero-wrap { background: radial-gradient(1200px 600px at 20% -10%, #13344a 0%, #0b2238 60%, #0b2238 100%); }
    }
    .brand-dot { width: 12px; height: 12px; background: #326ba8; border-radius: 50%; display: inline-block;}
    .text-muted-2 { color: #788ca2 !important;}
    .btn-dark-outline { border: 1px solid rgba(0,0,0,.20);}
  </style>
</head>
<body>

  <!-- NAVIGATION -->
  <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">
        <span class="brand-dot me-2"></span>Asset X
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMain">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
          <li class="nav-item"><a class="nav-link" href="{{url('reelly_projects')}}">Projects</a></li>
          <li class="nav-item"><a class="nav-link" href="">Properties</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Customers</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Marketing</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Deals</a></li>
          <li class="nav-item">
            <a class="btn btn-outline-dark btn-sm ms-lg-2" href="{{url('login')}}">Login</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-dark btn-sm ms-lg-2" href="#">Sign Up</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

        <main class="py-4">
            @yield('content')
        </main>
    <!-- FOOTER -->
  <footer class="border-top py-4 mt-auto">
    <div class="container d-flex flex-column flex-sm-row justify-content-between gap-2 small">
      <div>&copy; 2025 Asset X. All rights reserved.</div>
      <div class="d-flex gap-3">
        <a class="link-underline link-underline-opacity-0" href="#">Contact</a>
        <a class="link-underline link-underline-opacity-0" href="{{ route('privacy-policy') }}">Privacy</a>
        <a class="link-underline link-underline-opacity-0" href="{{ route('support') }}">Support</a>
        <a class="link-underline link-underline-opacity-0" href="#">Terms</a>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
