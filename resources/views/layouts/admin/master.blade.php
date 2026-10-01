@php
    $uiSetting = []; //getUISetting();
    $selectedCompany = []; //getSelectedCompany();

    $orgName = $currentOrg->name ?? config('app.name', 'Visitor Suite');
    $brand = $brand ?? [];
    $loginBg = data_get($brand, 'colors.login_bg', '#1e2a78');

    $primary = data_get($brand, 'colors.primary', '#000');

    $sidebar_color = data_get($brand, 'colors.sidebar_color', '#fff');
    $sidebar_font_color = data_get($brand, 'colors.sidebar_font_color', '#000');
    $sidebar_font_hover = data_get($brand, 'colors.sidebar_font_hover', '#000');
    $header = data_get($brand, 'colors.header', '#000');
    $loginBg = data_get($brand, 'colors.login_bg', '#1e2a78');
    $btnColor = data_get($brand, 'colors.button', '#fff');

@endphp
@auth
    @php
        $me = Auth::user();
        $role = $me->getRoleNames()->implode(', '); // "org admin, receptionist", etc.
    @endphp


@endauth
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-layout-mode="light" data-body-image="img-1" data-preloader="disable">


<head>

    <meta charset="utf-8" />
    <title>Dashboard | {{ $orgName }} </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- jsvectormap css -->
    <link href="{{ asset('assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">


    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">
   <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
{{-- <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet"> --}}
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">

</head>
<style>
    :root {
        --vs-primary: {{ $primary }};
        --vs-header: {{ $header }};
        --vs-loginbg: {{ $loginBg }};
        --vs-button: {{ $btnColor ?? '#FFFFFF' }};
        --vs-bg: {{ $c['surface'] ?? '#FFFFFF' }};
        --vs-text: {{ $c['text'] ?? '#0F172A' }};
        --vs-muted: {{ $c['muted'] ?? '#475569' }};
        --vs-primary: {{ $c['primary'] ?? '#1473E6' }};
        --vs-accent: {{ $c['accent'] ?? '#0EA5E9' }};
        --vs-success: {{ $c['success'] ?? '#22C55E' }};
        --vs-warning: {{ $c['warning'] ?? '#F59E0B' }};
        --vs-danger: {{ $c['danger'] ?? '#EF4444' }};

        --vs-radius: 16px;
        --vs-gap: 12px;
        --vs-tap: 56px;
        /* minimum touch target */
        --vs-shadow: 0 10px 24px rgba(0, 0, 0, .08);
    }

    [data-layout=vertical][data-sidebar=dark] .navbar-nav .nav-link {
        color: {{$sidebar_font_color}};
    }

    [data-layout=vertical][data-sidebar=dark] .navbar-nav .nav-link:hover {
        color: {{$sidebar_font_hover}};
    }

    .btn-primary {
        background: var(--vs-primary) !important;
        border-color: var(--vs-button) !important;
        color: var(--vs-button) !important;
    }

    select {
        /* background-color: #2a4562 !important; */
        color: #fff;
        /* optional: for better visibility */
    }

    .page-title-box {
        margin: 0px !important;
    }

    .navbar-menu .navbar-nav .nav-link[data-bs-toggle=collapse]:after,
    .navbar-brand-box,
    .nav-item {
        color: @if (!empty($uiSetting))
            {{ $uiSetting->admin_bt_color }}
        @else
            #eee
        @endif
        !important;
    }

    .navbar-brand-box,
    .nav-item {
        border-bottom: 1px solid @if (!empty($uiSetting))
            {{ $uiSetting->sidebar_border_color }}
        @else
            #ddd
        @endif
        ;
    }

    .logo-sm {
        background-color: @if (!empty($uiSetting))
            {{ $uiSetting->admin_user_sidebar_color }}
        @else
            transparent
        @endif
        !important;
    }

    .auth-one-bg {

        background-position: center;
        background-size: cover;
    }

    .text-right {
        text-align: right;
    }


    #searchTable th.col-customer,
    #cnTable td.col-customer,
    #cnTable th.col-customer,
    #searchTable td.col-customer {
        width: 200px;
        max-width: 200px;
        white-space: normal !important;
        /* override DataTables .nowrap */
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .item-details {
        min-height: 38px;
        /* your default control height */
        overflow: hidden;
        /* hide the internal scrollbar */
        resize: none;
        /* optional: prevent manual resize handle */
        box-sizing: border-box;
        /* include padding in height calc */
        line-height: 1.35;
        /* tweak if needed */
        vertical-align: top;
        /* table cell alignment */
    }

    /* keeps cells aligned nicely when a textarea grows */
    #invoice-items-table td {
        vertical-align: top;
    }

    body {
        background: var(--vs-bg);
        color: var(--vs-text);
    }

    /* Kiosk tiles */
    .vs-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 160px;
        border-radius: var(--vs-radius);
        box-shadow: var(--vs-shadow);
        background: #fff;
        color: var(--vs-text);
        text-align: center;
        border: 1px solid rgba(0, 0, 0, .06);
        transition: transform .06s ease, box-shadow .06s ease;
    }

    .vs-tile:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, .12);
    }

    .vs-tile .vs-icon {
        font-size: 42px;
        line-height: 1;
        margin-bottom: 10px;
    }

    .vs-tile-primary {
        background: #003f5c;
        color: #fff;
    }

    .vs-tile-2 {
        background: #bc5090;
        color: #fff;
    }


    .vs-tile-3 {
        background: #92b0d2;
        color: #fff;
    }


    .vs-tile-4 {
        background: #58508d;
        color: #fff;
    }

    /* Big buttons */
    .btn-vs {
        min-height: var(--vs-tap);
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.05rem;
    }

    .btn-vs-primary {
        background: var(--vs-primary);
        border-color: var(--vs-primary);
        color: #fff;
    }

    .btn-vs-success {
        background: var(--vs-success);
        border-color: var(--vs-success);
        color: #fff;
    }

    .btn-vs-warning {
        background: var(--vs-warning);
        border-color: var(--vs-warning);
        color: #111;
    }

    .btn-vs-danger {
        background: var(--vs-danger);
        border-color: var(--vs-danger);
        color: #fff;
    }

    .btn-vs:focus {
        outline: 3px solid rgba(20, 115, 230, .35);
        outline-offset: 2px;
    }

    /* Status chips */
    .chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: .25rem .6rem;
        border-radius: 999px;
        font-weight: 600;
    }

    .chip-approve {
        background: rgba(34, 197, 94, .12);
        color: var(--vs-success);
    }

    .chip-pending {
        background: rgba(245, 158, 11, .15);
        color: var(--vs-warning);
    }

    .chip-denied {
        background: rgba(239, 68, 68, .12);
        color: var(--vs-danger);
    }

    /* Lists for expected/checked-in */
    .vs-list .item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .8rem 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, .06);
    }

    .vs-list .title {
        font-weight: 700;
    }

    .vs-list .meta {
        color: var(--vs-muted);
        font-size: .9rem;
    }

    /* Card header accent */
    .card .card-header {
        border-bottom: none;
        font-weight: 700;
        background: linear-gradient(90deg, rgba(0, 0, 0, .02), rgba(0, 0, 0, 0));
    }

    /* Dark mode toggle (optional) */
    [data-theme="dark"] {
        --vs-bg: #0F1020;
        --vs-text: #fff;
        --vs-muted: #cbd5e1;
        --vs-surface: #181a2a;
    }

    .is-receptionist .page-content {
        padding: 20px !important;
    }

    .is-receptionist .footer {
        left: 0 !important;
        bottom: 20px;
    }
</style>
<style>
    /* ===== Reception Dashboard ===== */
    .vs-reception {
        position: relative;
        padding: 24px;
        min-height: calc(100vh - 120px);
        color: #e9eefb;
        background: radial-gradient(1200px 600px at 85% -10%, rgba(99, 102, 241, .35), transparent 60%),
            radial-gradient(900px 500px at 10% -20%, rgba(56, 189, 248, .30), transparent 60%),
            linear-gradient(135deg, #2b2f77 0%, #223061 35%, #162c58 60%, #112a52 100%);
        overflow: hidden;
    }

    /* Decorative faint blob */
    .vs-reception::after {
        content: "";
        position: absolute;
        right: -140px;
        top: -120px;
        width: 680px;
        height: 420px;
        border-radius: 60%;
        background: radial-gradient(closest-side, rgba(255, 255, 255, .18), rgba(255, 255, 255, 0));
        filter: blur(8px);
        pointer-events: none;
    }

    .vs-reception * {
        box-sizing: border-box;
    }

    /* Header */
    .vs-reception-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 18px;
    }

    .vs-greeting h1 {
        margin: 0;
        font-weight: 800;
        font-size: 36px;
        line-height: 1.1;
        color: #ffffff;
        text-shadow: 0 2px 12px rgba(0, 0, 0, .25);
    }

    .vs-greeting h1 span {
        filter: drop-shadow(0 2px 2px rgba(0, 0, 0, .2));
    }

    .vs-greeting h2 {
        margin: 6px 0 0;
        font-weight: 700;
        font-size: 28px;
        color: #ffffff;
        opacity: .95;
    }

    .vs-user-meta {
        text-align: right;
    }

    .vs-user-name {
        font-weight: 700;
    }

    .vs-user-role {
        color: #c6d0ea;
        font-size: .9rem;
    }

    /* KPI grid */
    .vs-kpi-grid {
        margin-top: 14px;
        display: grid;
        gap: 18px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .vs-kpi {
        border-radius: 20px;
        padding: 26px 22px;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
    }

    .vs-kpi .vs-kpi-value {
        font-size: 40px;
        font-weight: 800;
        margin-bottom: 6px;
        text-shadow: 0 2px 8px rgba(0, 0, 0, .25);
    }

    .vs-kpi .vs-kpi-label {
        opacity: .95;
        font-weight: 600;
        letter-spacing: .2px;
    }

    /* KPI colors */
    .vs-kpi-blue {
        background: linear-gradient(180deg, #5AA1FF 0%, #5D7BFF 100%);
    }

    .vs-kpi-mint {
        background: linear-gradient(180deg, #2dd4bf 0%, #22c55e 100%);
    }

    .vs-kpi-warm {
        background: linear-gradient(180deg, #f472b6 0%, #f59e0b 100%);
    }

    /* Actions */
    .vs-actions {
        margin-top: 18px;
        display: grid;
        gap: 18px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .vs-action {
        display: flex;
        align-items: center;
        gap: 16px;
        border-radius: 18px;
        padding: 22px;
        color: #fff;
        text-decoration: none;
        box-shadow: 0 10px 24px rgba(0, 0, 0, .25);
        transform: translateY(0);
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }

    .vs-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 34px rgba(0, 0, 0, .35);
        filter: brightness(1.05);
    }

    .vs-action .vs-action-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 28px;
        background: rgba(255, 255, 255, .18);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .15);
    }

    .vs-action-title {
        font-weight: 800;
        font-size: 20px;
        margin: 0;
    }

    .vs-action-sub {
        opacity: .85;
        margin-top: 2px;
    }

    /* Action colors */
    .vs-action-scan {
        background: linear-gradient(160deg, #06b6d4 0%, #0ea5e9 100%);
    }

    .vs-action-register {
        background: linear-gradient(160deg, #a78bfa 0%, #7c3aed 100%);
    }

    .vs-action-visits {
        background: linear-gradient(160deg, #f6c453 0%, #f59e0b 100%);
    }

    /* Panels (glass) */
    .vs-panels {
        margin-top: 10px;
        display: grid;
        gap: 15px;
        grid-template-columns: 1fr 1fr;
    }

    .vs-panel {
        background: rgba(17, 24, 39, .45);
        border: 1px solid rgba(255, 255, 255, .08);
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .35);
        backdrop-filter: blur(10px);
    }

    .vs-panel-title {
        font-weight: 800;
        color: #eaf1ff;
        margin-bottom: 12px;
        letter-spacing: .2px;
    }

    /* Lists */
    .vs-activity,
    .vs-approvals {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 12px;
    }

    .vs-activity li,
    .vs-approvals li {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 12px;
        background: rgba(255, 255, 255, .03);
        border: 1px solid rgba(255, 255, 255, .05);
    }

    .vs-activity .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, .25);
    }

    .vs-approvals .hourglass {
        filter: drop-shadow(0 1px 4px rgba(0, 0, 0, .25));
    }

    .vs-activity .text,
    .vs-approvals .text {
        color: #e6ecff;
        font-weight: 600;
    }

    .vs-activity .time,
    .vs-approvals .time {
        color: #c7cfe6;
        font-size: .9rem;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .vs-kpi-grid {
            grid-template-columns: 1fr 1fr;
        }

        .vs-actions {
            grid-template-columns: 1fr 1fr 1fr;
        }
    }

    @media (max-width: 992px) {
        .vs-reception-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .vs-user-meta {
            text-align: left;
        }

        .vs-actions {
            grid-template-columns: 1fr;
        }

        .vs-kpi-grid {
            grid-template-columns: 1fr;
        }

        .vs-panels {
            grid-template-columns: 1fr;
        }
    }

    #live-clock {
        font-family: 'Share Tech Mono', ui-monospace, SFMono-Regular, Menlo, monospace;
        letter-spacing: 3px;
        font-size: 24px;
        padding: 6px 12px;
        border-radius: 8px;
        background: linear-gradient(180deg, rgba(0, 0, 0, .22), rgba(255, 255, 255, .06));
        color: #B8F3FF;
        text-shadow: 0 0 8px rgba(0, 255, 255, .5);
        border: 1px solid rgba(0, 255, 255, .25);
    }

    .vs-panels table {
        color: #fff;
        border-collapse: collapse;
        width: 100%;
    }

    .vs-panels table th {
        color: #fff;
        padding: 8px 12px;
        text-align: left;

    }

    .vs-panels table td {
        color: #fff !important;
        padding: 8px 12px;
    }

    .vs-panels table td div {
        color: #fff;
    }

    /* Zebra striping */
    .vs-panels table tr:nth-child(odd) {
        background-color: rgba(255, 255, 255, 0.05) !important;

        /* subtle dark shade */
    }

    .vs-panels table tr:nth-child(even) {
        background-color: rgba(255, 255, 255, 0.12) !important;

        /* slightly brighter */
    }

    /* Optional hover highlight */
    .vs-panels table tr:hover {
        background-color: rgba(59, 130, 246, 0.25) !important;
        /* blue-ish hover */
    }



.select2-container--default .select2-selection--multiple {
        background-color: #fff;
        border: 1px solid #000 !important;
        color: #000 !important;
    }

    .select2-container--default .select2-selection__choice {
        /* background-color: #000 !important; */
        color: #000 !important;
        border: none !important;
        border-radius: 3px;
        padding: 2px 8px;
    }

    .select2-container--default .select2-results__option {
        color: #000 !important;
    }

    .select2-container--default .select2-selection__rendered {
        color: #000 !important;
    }




    /* Select2 height increase */
.filter-select + .select2-container .select2-selection--single {
    height: 40px !important;
    display: flex;
    align-items: center;
    border: 1px solid #ced4da;
}

.filter-select + .select2-container .select2-selection__rendered {
    line-height: 40px !important;
    padding-left: 12px;
}

.filter-select + .select2-container .select2-selection__arrow {
    height: 40px !important;
}


</style>

<body class="@role('receptionist') is-receptionist vs-reception @endrole">

    <!-- Begin page -->
    <div id="layout-wrapper">

        @unlessrole('receptionist')
            @include('layouts.admin.include.head')
            @include('layouts.admin.include.sidebar')
        @endunlessrole
        <div class="vertical-overlay"></div>

        <div class="main-content" style="@role('receptionist')margin-left:0;@endrole">
            <div class="page-content">
                @yield('main-content')
            </div>
            @unlessrole('receptionist')
                <footer class="footer border-top">
                    <div class="container-fluid">
                        <div class="row " style="padding-bottom:10px">
                            <div class="col-sm-10 ">
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> © Asset X Powered By vServe Group.
                            </div>
                            <div class="col-sm-2 text-end">

                            </div>
                        </div>
                    </div>
                </footer>
            @endunlessrole
        </div>

        @include('layouts.admin.include.footer')
