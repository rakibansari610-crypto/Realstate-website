@extends('layouts.admin.master')
@section('main-content')
@php
    $uiSetting = [];//getUISetting();
@endphp
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0" style="color:{{ $uiSetting->admin_t_color ?? '' }}">Dashboard Super Admin</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);" style="color:{{ $uiSetting->admin_t_color ?? '' }}">Dashboards</a>
                        </li>
                        <li class="breadcrumb-item active" style="color:{{ $uiSetting->admin_t_color ?? '' }}">Dashboard</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    

       
    
@endsection

@section('scripts')
@endsection
