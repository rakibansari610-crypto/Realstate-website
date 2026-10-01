@extends('layouts.admin.master')

@section('main-content')
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            /* select ke height ki css h yeh*/
            padding: 5px 12px;
        }

        /* Text vertically center karne ke liye */
        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 32px;
            /* height - padding ke hisaab se */
        }

        /* Dropdown arrow center */
        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .select-loader {
            font-style: italic;
            color: #999;
        }
    </style>

    @php $disabled = isset($isView) && $isView ? 'disabled' : ''; @endphp
    <div class="content container-fluid">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">Please fix the following:</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="page-title">Edit Floor Plan</h3>

        </div>

        <form method="post" action="{{ route('org.typical.units.store') }}">

            @csrf

            <div class="row g-3">

                {{-- Bedrooms --}}
                <div class="col-md-3">
                    <label class="form-label">rooms *</label>
                    <input type="number" name="bedrooms" step="0.5" class="form-control" required
                        value="{{ old('bedrooms', $typical->bedrooms ?? '') }}">
                </div>
                {{-- TYPE --}}
                <div class="col-md-3">

                    <label>Type</label>

                    <select name="type_name" id="type_name" class="form-select">

                        <option value="">Select</option>

                        @foreach ($types as $t)
                            <option value="{{ $t->id }}"
                                {{ old('type_name', $typical->type_name) == $t->id ? 'selected' : '' }}>

                                {{ $t->name }}

                            </option>
                        @endforeach

                    </select>

                </div>



                {{-- ROOM TYPE --}}
                <div class="col-md-3">

                    <label>Room Type</label>

                    <select name="room_type" id="room_type" class="form-select"
                        data-old="{{ old('room_type', $typical->room_type ?? '') }}">
                        <option value="">Select</option>

                    </select>

                </div>



                {{-- Currency --}}
                <div class="col-md-3">
                    <label class="form-label">Currency *</label>
                    <select name="currency_code" class="form-select" required>
                        <option value="">-- Select --</option>
                        @foreach ($countries as $ct)
                            <option value="{{ $ct->country_currency }}"
                                {{ old('currency_code', $typical->currency_code ?? '') == $ct->country_currency ? 'selected' : '' }}>
                                {{ $ct->country_currency }}
                            </option>
                        @endforeach
                    </select>

                </div>

                {{-- From Price --}}
                <div class="col-md-3">
                    <label class="form-label">From Price</label>
                    <input type="number" step="0.01" name="from_price" class="form-control"
                        value="{{ old('from_price', $typical->from_price ?? 0) }}">
                </div>

                {{-- To Price --}}
                <div class="col-md-3">
                    <label class="form-label">To Price</label>
                    <input type="number" step="0.01" name="to_price" class="form-control"
                        value="{{ old('to_price', $typical->to_price ?? 0) }}">
                </div>

                {{-- From Size Sqft --}}
                <div class="col-md-3">
                    <label class="form-label">From Size (Sqft)</label>
                    <input type="number" step="0.01" name="from_size_sqft" class="form-control"
                        value="{{ old('from_size_sqft', $typical->from_size_sqft ?? '') }}">
                </div>

                {{-- To Size Sqft --}}
                <div class="col-md-3">
                    <label class="form-label">To Size (Sqft)</label>
                    <input type="number" step="0.01" name="to_size_sqft" class="form-control"
                        value="{{ old('to_size_sqft', $typical->to_size_sqft ?? '') }}">
                </div>

                {{-- From Size m2 --}}
                <div class="col-md-3">
                    <label class="form-label">From Size (m²)</label>
                    <input type="number" step="0.0000001" name="from_size_m2" class="form-control"
                        value="{{ old('from_size_m2', $typical->from_size_m2 ?? '') }}">
                </div>

                {{-- To Size m2 --}}
                <div class="col-md-3">
                    <label class="form-label">To Size (m²)</label>
                    <input type="number" step="0.0000001" name="to_size_m2" class="form-control"
                        value="{{ old('to_size_m2', $typical->to_size_m2 ?? '') }}">
                </div>

                {{-- Sort Order --}}
                <div class="col-md-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control"
                        value="{{ old('sort_order', $typical->sort_order ?? 0) }}">
                </div>
                <input type="hidden" name="typical_id" value="{{ old('id', encrypt($typical->id) ?? '') }}">
                <input type="hidden" name="project_id" value="{{ $project->id }}">
            </div>

            <div class="mt-4">
                <button class="btn btn-primary">
                    Update Floor Plan
                </button>
                <a href="{{ route('org.projects.edit', $project->id ?? $typical->project_id) }}"
                    class="btn btn-link">Cancel</a>
            </div>
        </form>




    </div>



    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {

            var oldType = "{{ old('type_name', $typical->type_name) }}";
            var oldRoom = "{{ old('room_type', $typical->room_type) }}";

            if (oldType) {
                loadRoom(oldType, oldRoom);
            }


            $('#type_name').change(function() {

                var id = $(this).val();

                loadRoom(id, '');

            });


            function loadRoom(type_id, selected) {

                $('#room_type').html('<option>Loading</option>');

                $.ajax({

                    url: "{{ route('org.get-room-types') }}",

                    data: {
                        type_id: type_id
                    },

                    success: function(res) {

                        $('#room_type').empty();

                        $('#room_type').append(
                            '<option value="">Select</option>'
                        );

                        $.each(res, function(k, v) {

                            var sel = selected == v.name ?
                                'selected' :
                                '';

                            $('#room_type').append(
                                '<option value="' + v.name + '" ' + sel + '>' +
                                v.name +
                                '</option>'
                            );

                        });

                    }

                });

            }

        });
    </script>
@endsection
