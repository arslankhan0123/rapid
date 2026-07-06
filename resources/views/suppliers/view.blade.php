@extends('layouts.app')
@section('title')
    {{ __('messages.suppliers.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/int-tel/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">

@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.suppliers.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('suppliers.index') }}" class="btn btn-primary form-btn">{{ __('messages.suppliers.list') }} </a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['id' => 'editForm']) }}
                    {{ Form::hidden('id', $supplier->id, ['id' => 'supplier_id']) }}
                    @include('suppliers.view_fields')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/int-tel/js/intlTelInput.min.js') }}"></script>
    <script src="{{ asset('assets/js/int-tel/js/utils.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        'use strict';

        $(document).on('submit', '#editForm', function(event) {
            event.preventDefault();
            processingBtn('#editForm', '#btnEditSave', 'loading');
            let id = $('#supplier_id').val();

            $.ajax({
                url: route('suppliers.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('suppliers.index', );
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#editForm', '#btnEditSave');
                },
            });

        });

        $(document).ready(function() {
            $('#editgroupId').select2({
                width: '100%', // Set the width of the select element
                placeholder: 'Select groups', // Placeholder text
                allowClear: true // Allow clearing the selection
            });

            $('#languageId').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.placeholder.select_language') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });

            $('#countryId').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.placeholder.select_country') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });
            $('#currencyId').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.placeholder.select_currency') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });


            var input = document.querySelector("#phoneNumber");
            var iti = window.intlTelInput(input, {
                initialCountry: "ae", // Set default country to Dubai (UAE)
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                nationalMode: false // Ensure country code is visible and used
            });
            // Set the phone number with country code if available
            // var defaultNumber = "{{ $supplier->phone }}"; // Replace with the actual default phone number
            // if (defaultNumber) {
            //     iti.setNumber(defaultNumber);
            // }

        });
    </script>
@endsection
