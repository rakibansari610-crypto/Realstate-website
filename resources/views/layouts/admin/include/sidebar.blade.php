<div class="app-menu navbar-menu border-end" style="background-color: {{ $sidebar_color ?? '#fff' }};">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="" class="logo logo-dark">
            <span class="logo-sm">
                <img src="#" alt="LOGO" height="22">
            </span>
            <span class="logo-lg">
                <img src="#" alt="LOGO" height="45">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="" class="logo logo-light">
            <span class="logo-sm">

                @if (!empty($currentOrg) && $currentOrg->logo_path)
                    <img src="{{ asset('storage/' . $currentOrg->logo_path) }}" alt="" height="40">
                @else
                    <img src="{{ url('assets/images/logo.png') }}" alt="" height="22">
                @endif

            </span>
            <span class="logo-lg">

                @if (!empty($currentOrg) && $currentOrg->logo_path)
                    <img src="{{ asset('storage/' . $currentOrg->logo_path) }}" alt="" height="50">
                @else
                    <img src="{{ url('assets/images/logo.png') }}" alt="" height="50">
                @endif

            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                @role('super admin|org admin')
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('su.dashboard') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class="ri-dashboard-2-line"></i> <span data-key="t-widgets">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('su.org.index') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class="las la-key"></i> <span data-key="t-widgets">Organisations</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('org.developers.index') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class=" las la-briefcase"></i> <span data-key="t-widgets">Developers</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('org.projects.index') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class="las la-building"></i> <span data-key="t-widgets">Projects</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('units.index') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class="las la-building"></i> <span data-key="t-widgets">Property</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('org.leads.index') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class="las la-user-shield"></i> <span data-key="t-widgets">Leads</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('org.partners.index') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class="las la-users"></i> <span data-key="t-widgets">Partners</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('org.users.index') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class="las la-user-alt"></i> <span data-key="t-widgets">Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        @php
                            $isActive = request()->routeIs('taxonomies*') || request()->routeIs('taxonomies.*')|| request()->routeIs('privacy.*')|| request()->routeIs('org-support-*') ||request()->routeIs('org.country.*') || request()->routeIs('org.agent-index') || request()->routeIs('org.testimonial-index') ||request()->routeIs('org.blogs-index') || request()->routeIs('org.seo-meta.*') || request()->routeIs('org.menus.*');
                        @endphp

                        <a class="nav-link menu-link d-flex justify-content-between align-items-center {{ $isActive ? '' : 'collapsed' }}"
                            href="#sidebarProjects" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="sidebarProjects"
                            style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">

                            <div>
                                <i class="las la-building"></i>
                                <span data-key="t-widgets">Master</span>
                            </div>

                            <i class="las {{ $isActive ? 'la-angle-up' : 'la-angle-down' }} arrow-icon"></i>
                        </a>




                        <div class="collapse menu-dropdown {{ $isActive ? 'show' : '' }}" id="sidebarProjects">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ url('taxonomies/amenities') }}"
                                        class="nav-link {{ request()->routeIs('org.category.*') ? 'active' : '' }}"
                                        style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                        Amenities
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('taxonomies/currency') }}"
                                        class="nav-link {{ request()->routeIs('org.category.*') ? 'active' : '' }}"
                                        style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                        Currency
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ url('taxonomies/unit_type') }}"
                                        class="nav-link {{ request()->routeIs('org.category.*') ? 'active' : '' }}"
                                        style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                        Unit Type
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('org.country.index') }}"
                                        class="nav-link {{ request()->routeIs('org.country.*') ? 'active' : '' }}"
                                        style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                        Countries
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ url('taxonomies/parking_type') }}"
                                        class="nav-link {{ request()->routeIs('org.sub_category.*') ? 'active' : '' }}"
                                        style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                        Parking Type
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('privacy.index') }}"
                                        class="nav-link {{ request()->routeIs('privacy.*') ? 'active' : '' }}"
                                        style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                        Privacy Policy
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('terms.conditions.index') }}"
                                        class="nav-link {{ request()->routeIs('terms.*') ? 'active' : '' }}"
                                        style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                        Terms & Conditions
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('org-support-index') }}"
                                        class="nav-link {{ request()->routeIs('support/index.*') ? 'active' : '' }}"
                                        style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                        Support Requests
                                    </a>
                                </li>
                               <li class="nav-item">
                                    <a href="{{ route('org.agent-index') }}"
                                        class="nav-link {{ request()->routeIs('org.agent-index') ? 'active' : '' }}"
                                        style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">  Agent
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('org.testimonial-index') }}"
                                        class="nav-link {{ request()->routeIs('org.testimonial-index') ? 'active' : '' }}"
                                        style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">  Testimonial
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('org.blogs-index') }}"
                                        class="nav-link {{ request()->routeIs('org.blogs-index') ? 'active' : '' }}"
                                        style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">  Blog
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('org.seo-meta.index') }}"
                                        class="nav-link {{ request()->routeIs('org.seo-meta.index') ? 'active' : '' }}"
                                        style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">  SEO Meta
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('org.menus.index') }}"
                                        class="nav-link {{ request()->routeIs('org.menus.*') ? 'active' : '' }}"
                                        style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">  Header Menus
                                    </a>
                                </li>

                                {{-- <li class="nav-item">
                                    <a href="{{ route('org-contact-index') }}"
                                    class="nav-link {{ request()->routeIs('org-contact-*') ? 'active' : '' }}"
                                    style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                        Contact Inquiries
                                    </a>
                                </li> --}}

                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        @php
                            $isActive = request()->is('contact/inquiries/*');
                        @endphp

                        <a class="nav-link menu-link d-flex justify-content-between align-items-center {{ $isActive ? '' : 'collapsed' }}"
                            href="#sidebarContact"
                            data-bs-toggle="collapse"
                            role="button"
                            aria-expanded="{{ $isActive ? 'true' : 'false' }}"
                            aria-controls="sidebarContact"
                            style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">

                            <div>
                                <i class="las la-building"></i>
                                <span>Contact Inquiries</span>
                            </div>

                            <i class="las {{ $isActive ? 'la-angle-up' : 'la-angle-down' }} arrow-icon"></i>
                        </a>

                        <div class="collapse menu-dropdown {{ $isActive ? 'show' : '' }}" id="sidebarContact">
                            <ul class="nav nav-sm flex-column">

                                <li class="nav-item">
                                    <a href="{{ url('contact/inquiries/project') }}"
                                    class="nav-link {{ request()->is('contact/inquiries/project') ? 'active' : '' }}"
                                    style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                        Contact Project Inquiries
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ url('contact/inquiries/properties') }}"
                                    class="nav-link {{ request()->is('contact/inquiries/properties') ? 'active' : '' }}"
                                    style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                        Contact Property Inquiries
                                    </a>
                                </li>
                                 <li class="nav-item">
                                    <a href="{{ url('brochure/inquiries') }}"
                                    class="nav-link {{ request()->is('brochure/inquiries') ? 'active' : '' }}"
                                    style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                        Brochue Inquiries
                                    </a>
                                </li>
                                 <li class="nav-item">
                                    <a href="{{ url('contact/inquiries/contact') }}"
                                    class="nav-link {{ request()->is('contact/inquiries') ? 'active' : '' }}"
                                    style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                        Contact Inquiries
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('org.mail.edit') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class=" las la-envelope-square"></i> <span data-key="t-widgets">Mail Settings</span>
                        </a>
                    </li>
                @endrole
                @role('data entry|channel partner|property consultant|freelancer')
                    <li class="nav-item">
                        <a class="nav-link menu-link"
                            href="@if (in_array($role, ['admin', 'org admin'])) {{ route('org.dashboard') }}
                @elseif($role == 'property consultant')
                        {{ route('cp.dashboard') }}
                        @elseif($role == 'freelancer')
                        {{ route('freelancer.dashboard') }}
                @elseif($role == 'channel partner')
                        {{ route('cp.dashboard') }}
                        @elseif($role == 'data entry')
                        {{ route('cp.dashboard') }}
                @else
                        {{ route('default.dashboard') }} @endif"
                            style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                            <i class="ri-dashboard-2-line"></i> <span data-key="t-widgets">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('org.developers.index') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class=" las la-briefcase"></i> <span data-key="t-widgets">Developers</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('org.projects.index') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class="las la-building"></i> <span data-key="t-widgets">Projects</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('projects.units.index') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class="las la-building"></i> <span data-key="t-widgets">Property</span>
                        </a>
                    </li>
                    @role('channel partner|property consultant|freelancer')
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('org.leads.index') }}"
                                style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                                <i class="las la-user-shield"></i> <span data-key="t-widgets">Leads</span>
                            </a>
                        </li>
                    @endrole
                    @role('channel partner')
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('org.partners.index') }}"
                                style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                                <i class="las la-users"></i> <span data-key="t-widgets">Partners</span>
                            </a>
                        </li>
                    @endrole
                @endrole
                @role('org admin')
                    {{-- <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('org.dashboard') }}" style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                <i class="ri-dashboard-2-line"></i> <span data-key="t-widgets">Dashboard</span>
                </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{route('org.developers.index')}}" style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                        <i class=" las la-briefcase"></i> <span data-key="t-widgets">Developers</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{route('org.projects.index')}}" style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                        <i class="las la-building"></i> <span data-key="t-widgets">Projects</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{route('projects.units.index')}}" style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                        <i class="las la-building"></i> <span data-key="t-widgets">Property</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{route('org.leads.index')}}" style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                        <i class="las la-user-shield"></i> <span data-key="t-widgets">Leads</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{route('org.partners.index')}}" style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                        <i class="las la-users"></i> <span data-key="t-widgets">Partners</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{route('org.users.index')}}" style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                        <i class="las la-user-alt"></i> <span data-key="t-widgets">Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    @php
                    $isActive = request()->routeIs('taxonomies*') || request()->routeIs('taxonomies.*');
                    @endphp

                    <a class="nav-link menu-link d-flex justify-content-between align-items-center {{ $isActive ? '' : 'collapsed' }}" href="#sidebarProjects" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="sidebarProjects" style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">

                        <div>
                            <i class="las la-building"></i>
                            <span data-key="t-widgets">Master</span>
                        </div>

                        <i class="las {{ $isActive ? 'la-angle-up' : 'la-angle-down' }} arrow-icon"></i>
                    </a>




                    <div class="collapse menu-dropdown {{ $isActive ? 'show' : '' }}" id="sidebarProjects">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{url('taxonomies/amenities')}}" class="nav-link {{ request()->routeIs('org.category.*') ? 'active' : '' }}" style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                    Amenities
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url('taxonomies/currency')}}" class="nav-link {{ request()->routeIs('org.category.*') ? 'active' : '' }}" style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                    Currency
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url('taxonomies/unit_type')}}" class="nav-link {{ request()->routeIs('org.category.*') ? 'active' : '' }}" style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                    Unit Type
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url('taxonomies/parking_type')}}" class="nav-link {{ request()->routeIs('org.sub_category.*') ? 'active' : '' }}" style="color: {{ $uiSetting->admin_header_text_color ?? 'black' }}">
                                    Parking Type
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{route('org.mail.edit')}}" style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                        <i class=" las la-envelope-square"></i> <span data-key="t-widgets">Mail Settings</span>
                    </a>
                </li> --}}
                @endrole
                @role('employee')
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('org.visits.approvals') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class="las la-key"></i> <span data-key="t-widgets">Pending Approvals</span>
                        </a>
                    </li>
                @endrole
                @role('rm')
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('rm.dashboard') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class="ri-dashboard-2-line"></i> <span data-key="t-widgets">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('org.partners.index') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class="las la-users"></i> <span data-key="t-widgets">Partners</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('org.leads.index') }}"
                            style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                            <i class="las la-user-shield"></i> <span data-key="t-widgets">Leads</span>
                        </a>
                    </li>
                @endrole

                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('change.password') }}"
                        style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                        <i class="las la-key"></i> <span data-key="t-widgets">Change Password</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ url('logout') }}"
                        style="color: {{ $uiSetting->admin_header_text_color ?? '' }}">
                        <i class="las la-sign-out-alt"></i> <span data-key="t-widgets">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
