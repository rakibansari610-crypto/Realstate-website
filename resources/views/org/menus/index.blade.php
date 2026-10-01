@extends('layouts.admin.master')

@section('main-content')
<!-- jQuery UI CSS for smooth drag & drop placeholder -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<style type="text/css">
    /* Hide any floating Select2 container created for menu_status or route_name */
    #menu_status + .select2-container, 
    #route_name + .select2-container,
    .select2-container--default.select2-container--open,
    .select2-container--open {
        display: none !important;
    }

    .no-select2 + .select2-container {
        display: none !important;
    }

    /* Strict native styling for form inputs & select dropdowns - 100% alignment fix */
    #menu_status,
    #route_name,
    #menu,
    #url,
    #ext_url_link,
    .custom-form-select, 
    .custom-form-control {
        position: static !important;
        left: auto !important;
        top: auto !important;
        margin: 4px 0 0 0 !important;
        display: block !important;
        width: 100% !important;
        box-sizing: border-box !important;
        height: 42px !important;
        color: #212529 !important;
        background-color: #ffffff !important;
        border: 1px solid #ced4da !important;
        font-size: 14px !important;
        border-radius: 4px !important;
        padding: 8px 12px !important;
        line-height: 1.5 !important;
        box-shadow: none !important;
    }

    select.custom-form-select, #menu_status, #route_name {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        background-size: 16px 12px !important;
        padding-right: 36px !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
    }

    select option {
        color: #212529 !important;
        background-color: #ffffff !important;
        padding: 8px !important;
    }

    /* Nested Sortable CSS */
    ol {
        margin: 0;
        padding: 0;
    }

    ol.sortable {
        margin: 0;
        padding: 0;
        list-style-type: none;
    }

    ol.sortable ol {
        margin: 0 0 0 25px;
        padding: 0;
        list-style-type: none;
    }

    .sortable li {
        margin: 7px 0 0 0;
        padding: 0;
        position: relative;
    }

    .move_data {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        margin-bottom: 5px;
        border: 1px solid #d2d6de !important;
        background: #f8f9fa !important;
        padding: 10px 14px !important;
        margin: 0;
        cursor: grab !important;
        border-radius: 6px !important;
        font-weight: 600;
        font-size: 13px;
        color: #333;
        transition: background 0.15s ease, border-color 0.15s ease;
        user-select: none;
    }

    .move_data:active {
        cursor: grabbing !important;
    }

    .move_data:hover {
        background: #eef0f3 !important;
        border-color: #b0b5bc !important;
    }

    .drag-handle {
        font-size: 18px;
        color: #777;
        cursor: grab;
    }

    /* Material Switch CSS */
    .material-switch > input[type="checkbox"] {
        display: none;   
    }

    .material-switch > label {
        cursor: pointer;
        height: 0px;
        position: relative; 
        width: 40px;  
    }

    .material-switch > label::before {
        background: rgb(0, 0, 0);
        box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
        border-radius: 8px;
        content: '';
        height: 16px;
        margin-top: -8px;
        position: absolute;
        opacity: 0.3;
        transition: all 0.4s ease-in-out;
        width: 40px;
    }

    .material-switch > label::after {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
        content: '';
        height: 24px;
        left: -4px;
        margin-top: -8px;
        position: absolute;
        top: -4px;
        transition: all 0.3s ease-in-out;
        width: 24px;
    }

    .material-switch > input[type="checkbox"]:checked + label::before {
        background: #405189;
        opacity: 0.6;
    }

    .material-switch > input[type="checkbox"]:checked + label::after {
        background: #405189;
        left: 20px;
    }

    .placeholder {
        border: 2px dashed #405189 !important;
        background: rgba(64, 81, 137, 0.08) !important;
        border-radius: 6px;
        margin: 6px 0 !important;
        min-height: 42px !important;
    }

    .ui-sortable-helper {
        opacity: 0.95 !important;
    }

    .ui-sortable-helper div.move_data {
        background: #ffffff !important;
        border: 1px solid #405189 !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
</style>

<div class="row">
    <div class="col-12 mb-3">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">HEADER MENU MANAGEMENT</h4>
        </div>
    </div>

    @if(session('success')) 
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div> 
    @endif

    @if(session('error')) 
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div> 
    @endif
</div>

<div class="row">
    <!-- LEFT COLUMN: Add / Edit Item Form (Col 7) -->
    <div class="col-md-7 col-sm-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                <h5 class="card-title mb-0">{{ !empty($editData) ? 'Edit Item' : 'Add Item' }}</h5>
                @if(!empty($editData))
                    <a href="{{ route('org.menus.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="las la-plus"></i> Add New Item
                    </a>
                @endif
            </div>
            <div class="card-body">
                <form role="form" id="state_form" action="{{ route('org.menus.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="id" name="id" value="{{ $editData->id ?? '' }}">

                    <div class="mb-3">
                        <label for="menu" class="form-label font-weight-bold">Page Name / Title <span class="text-danger">*</span></label>
                        <input autofocus id="menu" name="title" placeholder="e.g. Buy, Rent, Projects, About Us" type="text" class="custom-form-control"
                            value="{{ old('title', $editData->title ?? '') }}" autocomplete="off" required>
                    </div>

                    <div class="row mb-3 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label d-block font-weight-bold">External URL</label>
                            <div class="material-switch">
                                <input id="ext_url" name="ext_url" type="checkbox" class="ext_url_chk" value="1"
                                    {{ (!empty($editData) && (str_starts_with($editData->url, 'http://') || str_starts_with($editData->url, 'https://'))) ? 'checked' : '' }}>
                                <label for="ext_url" class="label-success"></label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label d-block font-weight-bold">Open In New Tab</label>
                            <div class="material-switch">
                                <input id="open_new_tab" name="open_new_tab" type="checkbox" class="chk" value="1"
                                    {{ (!empty($editData) && $editData->target == '_blank') ? 'checked' : '' }}>
                                <label for="open_new_tab" class="label-success"></label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="ext_url_group" style="{{ (!empty($editData) && (str_starts_with($editData->url, 'http://') || str_starts_with($editData->url, 'https://'))) ? '' : 'display: none;' }}">
                        <label for="ext_url_link" class="form-label font-weight-bold">External URL Address</label>
                        <input id="ext_url_link" name="ext_url_link" placeholder="https://example.com" type="text" class="custom-form-control"
                            value="{{ old('url', $editData->url ?? '') }}" autocomplete="off">
                    </div>

                    <div class="mb-3" id="page_route_group">
                        <label for="route_name" class="form-label font-weight-bold">Select Page / Route</label>
                        <select id="route_name" name="route_name" class="custom-form-select no-select2">
                            <option value="">-- Custom URL or Select Page --</option>
                            @foreach($availableRoutes as $rName => $rLabel)
                                <option value="{{ $rName }}" {{ (!empty($editData) && $editData->route_name == $rName) ? 'selected' : '' }}>
                                    {{ $rLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="internal_url_group">
                        <label for="url" class="form-label font-weight-bold">Internal Path / URL</label>
                        <input id="url" name="url" placeholder="/buy or #" type="text" class="custom-form-control"
                            value="{{ old('url', $editData->url ?? '#') }}" autocomplete="off">
                    </div>

                    <div class="mb-3">
                        <label for="menu_status" class="form-label font-weight-bold">Status <span class="text-danger">*</span></label>
                        <select id="menu_status" name="status" class="custom-form-select no-select2" autocomplete="off" required>
                            <option value="active" {{ (empty($editData) || $editData->is_active == 1) ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ (!empty($editData) && $editData->is_active == 0) ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="mt-4 text-end">
                        <button class="btn btn-primary px-4" type="submit">
                            {{ !empty($editData) ? 'Update Item' : 'Submit' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: Record Drag & Drop List (Col 5) -->
    <div class="col-md-5 col-sm-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                <h5 class="card-title mb-0">Record</h5>
                <small class="text-muted"><i class="las la-info-circle"></i> Drag items to reorder or create sub-menus</small>
            </div>
            <div class="card-body">
                @if(count($menuTree) > 0)
                    <ol class="sortable ui-sortable mjs-nestedSortable-branch mjs-nestedSortable-expanded">
                        @foreach($menuTree as $item)
                            <li id="list_{{ $item->id }}">
                                <div class="move_data">
                                    <div class="d-flex align-items-center">
                                        <i class="las la-grip-vertical drag-handle me-2"></i>
                                        <span>({{ $item->id }}) <strong>{{ strtoupper($item->title) }}</strong></span>
                                        @if(!$item->is_active)
                                            <span class="badge bg-danger ms-2">Inactive</span>
                                        @endif
                                    </div>
                                    <div class="menu-options">
                                        <a href="{{ route('org.menus.edit', encrypt($item->id)) }}" title="Edit" class="text-primary me-2">
                                            <i class="las la-edit fs-16"></i>
                                        </a>
                                        <form action="{{ route('org.menus.destroy', encrypt($item->id)) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this page?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0 border-0" title="Delete">
                                                <i class="las la-trash fs-16"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @if(!empty($item->children_tree) && count($item->children_tree) > 0)
                                    <ol class="subteam_">
                                        @foreach($item->children_tree as $subItem)
                                            <li id="list_{{ $subItem->id }}">
                                                <div class="move_data">
                                                    <div class="d-flex align-items-center">
                                                        <i class="las la-grip-vertical drag-handle me-2"></i>
                                                        <span>({{ $subItem->id }}) {{ strtoupper($subItem->title) }}</span>
                                                        @if(!$subItem->is_active)
                                                            <span class="badge bg-danger ms-2">Inactive</span>
                                                        @endif
                                                    </div>
                                                    <div class="menu-options">
                                                        <a href="{{ route('org.menus.edit', encrypt($subItem->id)) }}" title="Edit" class="text-primary me-2">
                                                            <i class="las la-edit fs-16"></i>
                                                        </a>
                                                        <form action="{{ route('org.menus.destroy', encrypt($subItem->id)) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this page?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-link text-danger p-0 border-0" title="Delete">
                                                                <i class="las la-trash fs-16"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>

                                                @if(!empty($subItem->children_tree) && count($subItem->children_tree) > 0)
                                                    <ol class="subteam_">
                                                        @foreach($subItem->children_tree as $childSubItem)
                                                            <li id="list_{{ $childSubItem->id }}">
                                                                <div class="move_data">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="las la-grip-vertical drag-handle me-2"></i>
                                                                        <span>({{ $childSubItem->id }}) {{ strtoupper($childSubItem->title) }}</span>
                                                                        @if(!$childSubItem->is_active)
                                                                            <span class="badge bg-danger ms-2">Inactive</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="menu-options">
                                                                        <a href="{{ route('org.menus.edit', encrypt($childSubItem->id)) }}" title="Edit" class="text-primary me-2">
                                                                            <i class="las la-edit fs-16"></i>
                                                                        </a>
                                                                        <form action="{{ route('org.menus.destroy', encrypt($childSubItem->id)) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this page?');">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-link text-danger p-0 border-0" title="Delete">
                                                                                <i class="las la-trash fs-16"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ol>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                @else
                    <p class="text-center text-muted py-4">No menu items found. Add items using the form on the left.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Load jQuery UI & nestedSortable JS -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/nestedSortable/2.0.0/jquery.mjs.nestedSortable.min.js"></script>

<script>
$(document).ready(function () {
    // Destroy and remove any Select2 container initialized on menu_status or route_name
    function fixSelects() {
        if ($.fn.select2) {
            $('#menu_status, #route_name').each(function() {
                if ($(this).data('select2')) {
                    try { $(this).select2('destroy'); } catch(e) {}
                }
            });
        }
        $('#menu_status, #route_name').removeClass('select2-hidden-accessible');
        $('#menu_status + .select2-container, #route_name + .select2-container').remove();
    }

    fixSelects();
    setTimeout(fixSelects, 100);
    setTimeout(fixSelects, 300);

    // External URL Toggle behavior
    $('#ext_url').on('change', function() {
        if ($(this).is(':checked')) {
            $('#ext_url_group').show();
            $('#internal_url_group').hide();
        } else {
            $('#ext_url_group').hide();
            $('#internal_url_group').show();
        }
    });

    // Auto-fill path/url when route is selected
    $('#route_name').on('change', function() {
        let routeVal = $(this).val();
        if (routeVal) {
            let routePaths = {
                'buy.properties': '/buy',
                'rent.properties': '/rent',
                'project': '/project-lists',
                'developers': '/developers',
                'services': '/services',
                'about-us': '/about-us',
                'news': '/news',
                'contact-us': '/contact-us',
                'privacy-policy': '/privacy-policy',
                'terms-conditions': '/terms-conditions'
            };
            if (routePaths[routeVal]) {
                $('#url').val(routePaths[routeVal]);
            }
        }
    });

    // Initialize Nested Sortable Drag & Drop cleanly
    setTimeout(function() {
        $('ol.sortable').nestedSortable({
            disableNesting: 'no-nest',
            forcePlaceholderSize: true,
            handle: 'div.move_data',
            cancel: 'a, button, .menu-options',
            helper: 'clone',
            items: 'li',
            maxLevels: 3,
            opacity: 0.85,
            placeholder: 'placeholder',
            revert: 150,
            tabSize: 25,
            tolerance: 'pointer',
            toleranceElement: '> div',
            update: function () {
                var list = $(this).nestedSortable('toHierarchy', {startDepthCount: 0});
                $.ajax({
                    url: "{{ route('org.menus.save_order') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order: list
                    },
                    success: function (response) {
                        if (response.status === 'success' && typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Menu order updated!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Order save failed', xhr);
                    }
                });
            }
        });
    }, 200);
});
</script>
@endsection
