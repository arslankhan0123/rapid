@extends('layouts.app')
@section('title')
    {{ __('messages.lead.new_lead') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/int-tel/css/intlTelInput.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.lead.new_lead') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ url()->previous() }}"
                    class="btn btn-primary form-btn float-right-mobile">{{ __('messages.lead.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => 'leads.store', 'id' => 'createLead']) }}
                    @include('leads.fields')
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
        let utilsScript = "{{ asset('assets/js/int-tel/js/utils.min.js') }}";
        let phoneNo = "{{ old('prefix_code') . old('phone') }}";
        let isEdit = true;
        let createCustomerUrl = '{{ route('leads.contactAsPerCustomer') }}';
    </script>
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script src="{{ mix('assets/js/custom/phone-number-country-code.js') }}"></script>
    <script src="{{ mix('assets/js/leads/create-edit.js') }}"></script>

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
            // Parse the product data into a JavaScript object
            var products = @json($data['products']);

            // Handle change event on the product group dropdown
            $('#product_group_id').change(function() {
                var groupId = $(this).val();
                // Filter products based on the selected group
                var filteredProducts = products.filter(function(product) {
                    return product.item_group_id == groupId;
                });
                // Clear the current options in the product dropdown
                $('#product_id').empty();
                // Add a placeholder option
                $('#product_id').append(
                    '<option value="">{{ __('messages.lead.select_product') }}</option>');
                // Populate the product dropdown with filtered products
                $.each(filteredProducts, function(index, product) {
                    $('#product_id').append('<option value="' + product.id + '">' + product.title +
                        '</option>');
                });
            });



            let create_lead = route('leads.store');
            $(document).on('submit', '#createLead', function(event) {
                event.preventDefault();

                processingBtn("#btnSave", true);


                let description = $('<div />').
                html($('#createDescription').summernote('code'));
                let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

                if ($('#createDescription').summernote('isEmpty')) {
                    $('#createDescription').val('');
                } else if (empty) {
                    displayErrorMessage(
                        'Description field is not contain only white space');
                    processingBtn('#createLead', '#btnSave', 'reset');
                    return false;
                }
                $.ajax({
                    url: create_lead,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(result) {
                        if (result.success) {
                            displaySuccessMessage(result.message);
                            const url = route('leads.index', );
                            window.location.href = url;
                        }
                    },
                    error: function(result) {
                        displayErrorMessage(result.responseJSON.message);
                        processingBtn("#btnSave", false);
                    },
                    complete: function() {
                        processingBtn("#btnSave", false);
                    },
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
            // Get data from PHP variables
            var statesByCountry = @json($states);
            var citiesByState = @json($cities);
            var areasByCity = @json($areas);

            function populateDropdown(selectId, options) {
                var $select = $('#' + selectId);
                $select.empty();;
                $.each(options, function(index, item) {
                    $select.append(new Option(item.name, item.id));
                });
            }

            $('#country_id').on('change', function() {
                var selectedCountry = $(this).val();
                var states = statesByCountry[selectedCountry] || [];

                populateDropdown('state_id', states);
                $('#state_id').trigger('change'); // Trigger change event to update cities
            });

            $('#state_id').on('change', function() {
                var selectedState = $(this).val();
                var cities = citiesByState[selectedState] || [];

                populateDropdown('city_id', cities);
                $('#city_id').trigger('change'); // Trigger change event to update areas
            });

            $('#city_id').on('change', function() {
                var selectedCity = $(this).val();
                var areas = areasByCity[selectedCity] || [];

                populateDropdown('area_id', areas);
            });
        });
    </script>
@endsection
