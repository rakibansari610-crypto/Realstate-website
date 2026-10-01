<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-layout-mode="light" data-body-image="img-1" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Dashboard | Invexis </title>
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

<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
</head>
<style>
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

    .btn-primary {
        background-color: @if (!empty($uiSetting))
            {{ $uiSetting->admin_bt_color }}
        @else
            #000
        @endif
        !important;
    }

    .btn-secondary {
        background-color: @if (!empty($uiSetting))
            {{ $uiSetting->admin_l_bt_color }}
        @else
            #000
        @endif
        !important;
    }

    .table-colored thead th {
        background:@if (!empty($uiSetting))
            {{ $uiSetting->admin_table_header_bg_color }}
        @else
            #e9f0ff
        @endif
        ;
        /* header bg */
        color:{{ $uiSetting->admin_table_header_text_color ?? '#000' }}
    }

    /* Body rows */
    .table-colored tbody tr:nth-child(odd) td {

        /* odd rows */
        background:@if (!empty($uiSetting))
            {{ $uiSetting->admin_table_row_even_bg_color }}
        @else
            #f9fbff
        @endif
        ;
    }

    .table-colored tbody tr:nth-child(even) td {

        /* odd rows */
        background:@if (!empty($uiSetting))
            {{ $uiSetting->admin_table_row_odd_bg_color }}
        @else
            # background: #f3f6ff;

        @endif
        ;
    }

    /* Optional hover */
    .table-colored tbody tr:hover td {
        background: #e6f2ff;
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
</style>

<body>

    <!-- Begin page -->


