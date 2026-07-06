@extends('layouts.app')
@section('title')
    {{ __('messages.suppliers.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/int-tel/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <style>
        #phoneNumber {
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
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.suppliers.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('suppliers.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.common.list') }}</a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['id' => 'editForm']) }}
                    {{ Form::hidden('id', $supplier->id, ['id' => 'supplier_id']) }}
                    @include('suppliers.edit_fields')
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
            var formData = new FormData(this);

            $.ajax({
                url: route('suppliers.update', id),
                type: 'POST',
                data: formData,
                contentType: false, // Prevent jQuery from setting the content type
                processData: false, // Prevent jQuery from processing the data
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

            //multiple file uploads

            // Add a new form field
            $('#addField').click(function() {
                var newField = `
            <div class="form-group row mb-3">
                <div class="col-md-4">
                    <label for="doc_name" class="form-label">Name:</label>
                    <input type="text" name="doc_name[]" class="form-control" >
                </div>
                <div class="col-md-4">
                    <label for="file" class="form-label">{{ __('messages.employees.file_pdf') }}, {{ __('messages.employees.max_size') }}</label>
                    <input type="file" name="file[]" class="form-control" accept="application/pdf" >
                </div>
                <div class="col-md-3">
                    <label for="expiry_date" class="form-label">Expiry Date:</label>
                    <input type="date" name="expiry_date[]" class="form-control" >
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger removeField"><i class="fa fa-minus" aria-hidden="true"></i></button>
                </div>
            </div>`;

                $('#formContainer').append(newField);
            });

            // Remove a form field
            $(document).on('click', '.removeField', function() {
                $(this).closest('.form-group').remove();
            });

            $(document).on('click', '.fileDeleteBtn', function(event) {
                let fileId = $(event.currentTarget).data('id');
                let row = $(event.currentTarget).closest('tr');
                row.addClass('disabled-row');


                swal({
                        title: Lang.get('messages.common.delete') + '!',
                        text: Lang.get('messages.common.delete_confirm_common') + ' "' +
                            "{{ __('messages.employees.file') }}" + '"?',
                        type: 'warning',
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        confirmButtonColor: '#6777ef',
                        cancelButtonColor: '#d33',
                        cancelButtonText: Lang.get('messages.common.no'),
                        confirmButtonText: Lang.get('messages.common.yes'),
                    },
                    function() {
                        deleteItemAjax(route('suppliers.file.delete', fileId),
                            "Document", row, null);
                    });

            });

            function deleteItemAjax(url, header, row, callFunction = null) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(obj) {
                        if (obj.success) {
                            row.hide();
                        }
                        swal({
                            title: Lang.get('messages.common.deleted'),
                            text: header + Lang.get('messages.common.has_been_delete'),
                            type: 'success',
                            confirmButtonText: Lang.get('messages.common.ok'),
                            confirmButtonColor: '#6777ef',
                            timer: 2000,
                        });
                        if (callFunction) {
                            eval(callFunction);
                        }
                    },
                    error: function(data) {
                        swal({
                            title: '',
                            text: data.responseJSON.message,
                            type: 'error',
                            confirmButtonText: Lang.get('messages.common.ok'),
                            confirmButtonColor: '#6777ef',
                            timer: 5000,
                        });
                    },
                });
            }

        });
    </script>
@endsection
