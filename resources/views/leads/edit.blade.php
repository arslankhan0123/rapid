@extends('layouts.app')
@section('title')
    {{ __('messages.lead.edit_lead') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/int-tel/css/intlTelInput.css') }}">
    <style>
        #mobile_number {
            padding-left: 68px;
            /* Adjust padding to fit the flag and country code */
            padding-right: 10px;
            /* Adjust as necessary */
            height: 40px;
            /* Adjust the height as necessary */
        }

        .iti__flag-container {
            display: flex;
            align-items: center;
        }

        .iti__selected-flag {
            width: 50px;
            /* Adjust width to fit flag and code */
            display: flex;
            align-items: center;
        }

        .iti__flag {
            margin-right: 8px;
            /* Space between flag and country code */
        }

        .iti__country-name {
            display: inline;
            /* Show the country name/code */
            margin-right: 4px;
            /* Adjust spacing as needed */
        }

        .iti__selected-dial-code {
            display: inline;
            /* Show the dial code */
            margin-right: 8px;
            /* Adjust spacing as needed */
        }
    </style>
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.lead.edit_lead') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ url()->previous() }}"
                    class="btn btn-primary form-btn float-right-mobile">{{ __('messages.lead.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            <div class="card">
                <div class="card-body">
                    {{ Form::model($lead, ['route' => ['leads.update', $lead->id], 'method' => 'put', 'id' => 'editLead']) }}
                    @include('leads.edit_fields')
                    {{ Form::close() }}
                </div>

            </div>
        </div>
    </section>
    @include('tags.common_tag_modal')
    @include('lead_sources.add_modal')
    @include('leads.lead_status_modal')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/int-tel/js/intlTelInput.min.js') }}"></script>
    <script src="{{ asset('assets/js/int-tel/js/utils.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let isEdit = true;
        let createCustomerUrl = '{{ route('leads.contactAsPerCustomer') }}';
    </script>
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    {{-- <script src="{{ mix('assets/js/custom/phone-number-country-code.js') }}"></script> --}}
    {{-- <script src="{{mix('assets/js/leads/create-edit.js')}}"></script> --}}


    <script>
        $(document).ready(function() {



            $('#product_group_id').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#product_id').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#assignee').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#source_id').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#status_Id').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#language_Id').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#country_id').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#state_id').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#city_id').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#area_id').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });

            var input = document.querySelector("#mobile_number");
            var iti = window.intlTelInput(input, {
                initialCountry: "ae", // Set default country to Dubai (UAE)
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                nationalMode: false, // Ensure country code is visible and used
                autoPlaceholder: "off" // Disable the placeholder
            });

            // Ensure products data is available and correctly formatted
            var products = @json($data['products']);
            // Function to populate products based on the selected group
            function updateProductDropdown(groupId, selectedProductId) {
                var filteredProducts = products.filter(function(product) {
                    return product.item_group_id == groupId;
                });

                // Clear the current options in the product dropdown
                $('#product_id').empty();

                // Add a placeholder option
                $('#product_id').append(
                    '<option value="">{{ __('messages.lead.select_product') }}</option>'
                );

                // Populate the product dropdown with filtered products
                $.each(filteredProducts, function(index, product) {
                    $('#product_id').append(
                        '<option value="' + product.id + '"' +
                        (product.id == selectedProductId ? ' selected' : '') + '>' +
                        product.title +
                        '</option>'
                    );
                });
            }

            // Handle change event on the product group dropdown
            $('#product_group_id').change(function() {
                var groupId = $(this).val();
                var selectedProductId = $('#product_id').val(); // Get the currently selected product

                // Update product dropdown based on the selected group
                updateProductDropdown(groupId, selectedProductId);
            });

            // For initial load in editing mode, populate dropdown based on pre-selected values
            function initializeDropdowns() {
                var initialGroupId = $('#product_group_id').val();
                var initialSelectedProductId =
                    '{{ $lead->product_id ?? '' }}'; // Ensure this value is correctly set


                // Update product dropdown based on the initial group
                if (initialGroupId) {
                    updateProductDropdown(initialGroupId, initialSelectedProductId);
                }
            }

            initializeDropdowns();


            $('#editLead').on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission
                processingBtn("#btnSave", true);
                var form = $(this);
                var url = form.attr('action'); // Get the form action URL
                var method = form.attr('method'); // Get the form method
                var data = form.serialize(); // Serialize form data

                $.ajax({
                    url: url,
                    method: method,
                    data: data,
                    success: function(response) {
                        displaySuccessMessage(response.message);
                        const url = route('leads.index', );
                        window.location.href = url;
                        processingBtn("#btnSave", false);
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = '';
                        $.each(errors, function(key, value) {
                            errorMessages += value[0] + '<br>';
                        });
                        processingBtn("#btnSave", false);
                        $('#errorMessages').html(errorMessages).show();
                    }
                });
            });
        });

        function processingBtn(btn, status = true) {
            if (status) {
                $(btn).prop('disabled', true);
                $(btn).html("Processing.....");
            } else {
                $(btn).prop('disabled', false);
                $(btn).html({{ __('messages.common.submit') }});
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            var states = @json($states);
            var cities = @json($cities);
            var areas = @json($areas);

            function populateDropdown(selectId, options) {
                var $select = $(selectId);
                $select.empty();
                $.each(options, function(id, name) {
                    $select.append($('<option>', {
                        value: id,
                        text: name
                    }));
                });
            }

            // Populate state dropdown based on the selected country
            $('#country_id').change(function() {
                var countryId = $(this).val();
                var filteredStates = states[countryId] || [];
                populateDropdown('#state_id', filteredStates.reduce((acc, state) => {
                    acc[state.id] = state.name;
                    return acc;
                }, {}));
                $('#state_id').trigger('change');
            });

            // Populate city dropdown based on the selected state
            $('#state_id').change(function() {
                var stateId = $(this).val();
                var filteredCities = cities[stateId] || [];
                populateDropdown('#city_id', filteredCities.reduce((acc, city) => {
                    acc[city.id] = city.name;
                    return acc;
                }, {}));
                $('#city_id').trigger('change');
            });

            // Populate area dropdown based on the selected city
            $('#city_id').change(function() {
                var cityId = $(this).val();
                var filteredAreas = areas[cityId] || [];
                populateDropdown('#area_id', filteredAreas.reduce((acc, area) => {
                    acc[area.id] = area.name;
                    return acc;
                }, {}));
            });

            // Trigger change events on page load to populate the dropdowns with the selected values
            $('#country_id').trigger('change');
        });
    </script>
@endsection
