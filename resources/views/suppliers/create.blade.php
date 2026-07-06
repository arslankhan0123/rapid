@extends('layouts.app')
@section('title')
    {{ __('messages.suppliers.add') }}
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
            <h1>{{ __('messages.suppliers.add') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('suppliers.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.suppliers.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['id' => 'addNewFormDepartmentNew']) }}
                    <div class="card-body">
                        {{ Form::open(['route' => 'customers.store', 'id' => 'createCustomer', 'novalidate']) }}
                        @include('suppliers.fields')
                        {{ Form::close() }}
                    </div>
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
        // let departmentNewCreateUrl = route('suppliers.store');
        // $(document).on('submit', '#addNewFormDepartmentNew', function(event) {
        //     event.preventDefault();
        //     processingBtn('#addNewFormDepartmentNew', '#btnSave', 'loading');
        //     var formData = new FormData(this);
        //     $.ajax({
        //         url: departmentNewCreateUrl,
        //         type: 'POST',
        //         data: formData,
        //         contentType: false, // Prevent jQuery from setting the content type
        //         processData: false, // Prevent jQuery from processing the data
        //         success: function(result) {
        //             if (result.success) {
        //                 displaySuccessMessage(result.message);
        //                 const url = route('suppliers.index', );
        //                 window.location.href = url;
        //             }
        //         },
        //         error: function(result) {
        //             displayErrorMessage(result.responseJSON.message);
        //         },
        //         complete: function() {
        //             processingBtn('#addNewFormDepartmentNew', '#btnSave');
        //         },
        //     });
        // });

        let departmentNewCreateUrl = route('suppliers.store');

        const input = document.querySelector("#phoneNumber");
        const errorMsg = document.querySelector("#error-msg");
        const validMsg = document.querySelector("#valid-msg");
        const prefixInput = document.querySelector("#prefix_code");

        const iti = window.intlTelInput(input, {
            initialCountry: "ae",
            nationalMode: false,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        });

        function resetPhoneValidation() {
            input.classList.remove("is-invalid");
            errorMsg.classList.add("hide");
            validMsg.classList.add("hide");
        }

        function validatePhoneNumber() {
            resetPhoneValidation();
            const number = input.value.trim();

            if (number) {
                if (iti.isValidNumber()) {
                    prefixInput.value = iti.getSelectedCountryData().dialCode;
                    validMsg.classList.remove("hide");
                    return true;
                } else {
                    input.classList.add("is-invalid");
                    errorMsg.textContent = "Invalid phone number for selected country.";
                    errorMsg.classList.remove("hide");
                    prefixInput.value = '';
                    return false;
                }
            }

            // allow blank if not required
            prefixInput.value = '';
            return true;
        }

        // Live validation while typing
        input.addEventListener("input", validatePhoneNumber);

        // Handle submit via AJAX
        $(document).on('submit', '#addNewFormDepartmentNew', function(event) {
            event.preventDefault();

            // Validate phone before submission
            if (!validatePhoneNumber()) {
                return false;
            }

            processingBtn('#addNewFormDepartmentNew', '#btnSave', 'loading');
            var formData = new FormData(this);

            $.ajax({
                url: departmentNewCreateUrl,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('suppliers.index');
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#addNewFormDepartmentNew', '#btnSave');
                },
            });
        });


        $(document).ready(function() {
            $('#groupId').select2({
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
            // var defaultNumber = "+8801600214050"; // Replace with the actual default phone number
            // if (defaultNumber) {
            //     iti.setNumber(defaultNumber);
            // }

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

        });
    </script>
    <script>
        const input = document.querySelector("#phoneNumber");
        const errorMsg = document.querySelector("#error-msg");
        const validMsg = document.querySelector("#valid-msg");
        const prefixInput = document.querySelector("#prefix_code");

        const iti = window.intlTelInput(input, {
            initialCountry: "ae",
            nationalMode: false,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        });

        function reset() {
            input.classList.remove("is-invalid");
            errorMsg.classList.add("hide");
            validMsg.classList.add("hide");
        }

        function validatePhone() {
            reset();
            const number = input.value.trim();
            if (number) {
                if (iti.isValidNumber()) {
                    prefixInput.value = iti.getSelectedCountryData().dialCode;
                    validMsg.classList.remove("hide");
                } else {
                    input.classList.add("is-invalid");
                    errorMsg.textContent = "Invalid phone number for selected country.";
                    errorMsg.classList.remove("hide");
                    prefixInput.value = '';
                }
            } else {
                prefixInput.value = '';
            }
        }

        // Instant validation while typing
        input.addEventListener("input", validatePhone);

        // Extra validation on form submit
        document.querySelector("#your-form-id").addEventListener("submit", function(e) {
            if (!iti.isValidNumber()) {
                e.preventDefault();
                input.classList.add("is-invalid");
                errorMsg.textContent = "Please enter a valid phone number.";
                errorMsg.classList.remove("hide");
            } else {
                prefixInput.value = iti.getSelectedCountryData().dialCode;
            }
        });
    </script>
@endsection
