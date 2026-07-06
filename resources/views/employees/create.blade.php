@extends('layouts.app')
@section('title')
    {{ __('messages.employees.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/int-tel/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .employeeForm {
            background: #f4f6f9;
            border-radius: 5px;
        }


        #phone {
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
            <h1>{{ __('messages.employees.add_employee') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('employees.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.employees.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">

                <div class="card-body">

                    <div style="width:100%;">

                        {{ Form::open(['id' => 'addNewForm']) }}

                        <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;">
                            <h3>{{ __('messages.employees.basic_information') }}</h3>
                        </div>
                        <div class="modal-body employeeForm">

                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('code', __('messages.employees.code')) }}<span class="required">*</span>
                                    {{ Form::text('code', 'SM-' . $nextNumber, ['class' => 'form-control', 'required', 'readonly', 'id' => 'code', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    {{ Form::label('title', __('messages.employees.employee_name')) }}<span
                                        class="required">*</span>
                                    {{ Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'name', 'autocomplete' => 'off']) }}
                                </div>
                                {{-- <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('employment_type', __('messages.employees.employment_type')) }}
                                    {{ Form::select('employment_type', ['casual' => 'Casual', 'rental' => 'Rental'], null, ['class' => 'form-control', 'id' => 'selectEmpType', 'autocomplete' => 'off', 'placeholder' => __('messages.employees.select_employment_type')]) }}
                                </div> --}}

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('title', __('messages.employees.departments')) }}<span
                                        class="required">*</span>
                                    {{ Form::select('department_id', $departments, null, ['class' => 'form-control', 'required', 'id' => 'departmentSelect', 'autocomplete' => 'off', 'placeholder' => __('messages.employees.select_department')]) }}
                                </div>
                                {{-- <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('sub_department_id', __('messages.department.sub_departments')) }}
                                    {{ Form::select('sub_department_id', $subDepartments->pluck('name', 'id'), null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'subDepartmentSelect']) }}
                                </div> --}}

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('designation_id', __('messages.employees.designations')) }}<span
                                        class="required">*</span>
                                    {{ Form::select('designation_id', $designations->pluck('name', 'id'), null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'id' => 'designationSelect']) }}
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('title', __('messages.employees.phone')) }}
                                    {{ Form::text('phone', null, ['class' => 'form-control', 'id' => 'phone', 'autocomplete' => 'off']) }}
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('title', __('messages.employees.email')) }}
                                    {{ Form::email('email', null, ['class' => 'form-control', 'id' => 'email', 'autocomplete' => 'off']) }}
                                </div>


                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('title', __('messages.employees.joining_date')) }}<span
                                        class="required">*</span>
                                    {{ Form::date('join_date', null, ['class' => 'form-control', 'id' => 'join_date', 'required', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('title', __('messages.employees.dob')) }}
                                    {{ Form::date('dob', null, ['class' => 'form-control', 'id' => 'dob', 'autocomplete' => 'off']) }}
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('marital_status', __('messages.employees.marital_status')) }}
                                    {{ Form::select('marital_status', ['married' => 'Married', 'unmarried' => 'Unmarried', 'divorced' => 'Divorced'], null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'marital_status', 'placeholder' => __('messages.employees.select_marital_status')]) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('blood_group', __('messages.employees.blood_group')) }}
                                    {{ Form::select(
                                        'blood_group',
                                        [
                                            'A+' => 'A+',
                                            'A-' => 'A-',
                                            'B+' => 'B+',
                                            'B-' => 'B-',
                                            'AB+' => 'AB+',
                                            'AB-' => 'AB-',
                                            'O+' => 'O+',
                                            'O-' => 'O-',
                                        ],
                                        null,
                                        [
                                            'class' => 'form-control',
                                            'id' => 'selectBloodGroup',
                                            'autocomplete' => 'off',
                                            'placeholder' => __('messages.employees.select_blood_group'),
                                        ],
                                    ) }}
                                </div>

                                {{-- <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('religion', __('messages.employees.religion')) }}
                                    {{ Form::text('religion', null, ['class' => 'form-control', 'id' => 'religion', 'autocomplete' => 'off']) }}
                                </div> --}}

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('gender', __('messages.employees.gender')) }}
                                    {{ Form::select('gender', ['male' => 'Male', 'female' => 'Female', 'other' => 'Others'], null, ['class' => 'form-control', 'id' => 'selectGender', 'autocomplete' => 'off', 'placeholder' => __('messages.employees.select_gender')]) }}
                                </div>

                                <div class="form-group col-md-3 col-sm-12">
                                    {{ Form::label('country', __('messages.employees.country')) }}
                                    {{ Form::select('country', $countries, null, ['id' => 'countryId', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_country')]) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('is_working', __('messages.employees.status')) }}
                                    {{ Form::select('status', ['1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                </div>


                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('basic_salary', __('messages.employees.basic_salary')) }} <span
                                        class="required">*</span>
                                    {{ Form::number('basic_salary', null, ['class' => 'form-control', 'id' => 'basic_salary', 'required', 'autocomplete' => 'off']) }}
                                </div>

                                {{-- <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('transport_allowance', __('messages.employees.transport_allowance')) }}
                                    {{ Form::number('transport_allowance', null, ['class' => 'form-control', 'id' => 'transport_allowance', 'autocomplete' => 'off']) }}
                                </div> --}}

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('gross_salary', __('messages.employees.gross_salary')) }}
                                    {{ Form::number('gross_salary', null, ['class' => 'form-control', 'id' => 'gross_salary', 'autocomplete' => 'off', 'readonly' => true]) }}
                                </div>

                                <div class="form-group col-sm-12 col-md-3" id="hourlyRateContainer" style="display: none;">
                                    {{ Form::label('hourly_rate', __('messages.employees.hourly_rate')) }}
                                    {{ Form::number('hourly_rate', null, ['class' => 'form-control', 'id' => 'hourly_rate', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('hourly_rate', __('messages.branches.name')) }}
                                    {{ Form::select('branch_id', $usersBranches ?? [], null, ['class' => 'form-control select2']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('hourly_rate', __('messages.employees.absent_allowance_deduction')) }}
                                    {{ Form::number('absent_allowance_deduction', $employee->absent_allowance_deduction ?? 0, ['class' => 'form-control', 'step' => 'any']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    <div class="form-check mt-4">
                                        {{ Form::checkbox('insurance_apply', 1, null, [
                                            'class' => 'form-check-input',
                                            'id' => 'insurance_apply',
                                            'style' => 'transform: scale(1.5); width: 20px; height: 20px;'
                                        ]) }}
                                        {{ Form::label('insurance_apply', __('messages.employees.insurance_apply'), ['class' => 'form-check-label ms-2 ml-3']) }}
                                    </div>
                                </div>

                            </div>
                        </div>


                        {{-- cards no starts  here --}}
                        <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                            <h3>{{ __('messages.employees.identificatin') }}</h3>
                        </div>
                        <div class="modal-body employeeForm">
                            <div class="row">
                                <!-- First Column: IQAMA Number and Expiry Date -->
                                <div class="form-group col-sm-12 col-md-4">
                                    {{ Form::label('iqama_no', __('messages.employees.iqama_no')) }}<span
                                        class="required">*</span>
                                    {{ Form::text('iqama_no', null, ['class' => 'form-control', 'required', 'id' => 'iqama_no', 'autocomplete' => 'off']) }}

                                    {{ Form::label('iqama_no_expiry_date', __('messages.employees.iqama_no_expiry_date'), ['class' => 'mt-3']) }}
                                    {{ Form::text('iqama_no_expiry_date', null, ['class' => 'form-control datepicker']) }}
                                </div>

                                <!-- Second Column: Passport Number and Expiry Date -->
                                <div class="form-group col-sm-12 col-md-4">
                                    {{ Form::label('passport', __('messages.employees.passport')) }}
                                    {{ Form::text('passport', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}

                                    {{ Form::label('passport_expiry_date', __('messages.employees.passport_expiry_date'), ['class' => 'mt-3']) }}
                                    {{ Form::text('passport_expiry_date', null, ['class' => 'form-control datepicker', 'autocomplete' => 'off']) }}
                                </div>

                                <!-- Third Column: TUV Number and Expiry Date -->
                                {{-- <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('tuv_no', __('messages.employees.tuv_no')) }}
                                    {{ Form::text('tuv_no', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}

                                    {{ Form::label('tuv_no_expiry_date', __('messages.employees.tuv_no_expiry_date'), ['class' => 'mt-3']) }}
                                    {{ Form::date('tuv_no_expiry_date', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                </div> --}}

                                <!-- Fourth Column: Driving License Number and Expiry Date -->
                                <div class="form-group col-sm-12 col-md-4">
                                    {{ Form::label('driving_license_no', __('messages.employees.driving_license_no')) }}
                                    {{ Form::text('driving_license_no', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}

                                    {{ Form::label('driving_license_expiry_date', __('messages.employees.driving_license_expiry_date'), ['class' => 'mt-3']) }}
                                    {{ Form::text('driving_license_expiry_date', null, ['class' => 'form-control datepicker']) }}
                                </div>


                            </div>
                        </div>
                        {{-- Banking starts  here --}}
                        <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                            <h3>{{ __('messages.employees.bank') }}</h3>
                        </div>
                        <div class="modal-body employeeForm">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    {{ Form::label('bank_name', __('messages.employees.bank_name')) }}
                                    {{ Form::text('bank_name', null, ['class' => 'form-control', 'id' => 'bank_name', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    {{ Form::label('branch_name', __('messages.employees.branch_name')) }}
                                    {{ Form::text('branch_name', null, ['class' => 'form-control', 'id' => 'branch_name', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    {{ Form::label('bank_account_no', __('messages.employees.bank_account_no')) }}
                                    {{ Form::text('bank_account_no', null, ['class' => 'form-control', 'id' => 'bank_account_no', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    {{ Form::label('iban_num', __('messages.employees.iban_num')) }}
                                    {{ Form::text('iban_num', null, ['class' => 'form-control', 'id' => 'iban_num', 'autocomplete' => 'off']) }}
                                </div>
                            </div>
                        </div>

                        {{-- next here --}}


                        {{-- cards no ends  here --}}

                        <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                            <h3>{{ __('messages.employees.image') }}</h3>
                        </div>
                        <div class="modal-body employeeForm">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    {{ Form::label('image', __('messages.employees.image') . ', ' . __('messages.employees.image_size')) }}
                                    {{ Form::file('image', ['class' => 'form-control', 'id' => 'image', 'accept' => 'image/*']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 image_preview">
                                    <img id="imagePreview" src="" alt="Image Preview"
                                        style="display: none; max-width: 50%; height: auto;border-radius:5px;" />
                                </div>
                            </div>
                        </div>

                        {{-- <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                            <h3>{{ __('messages.employees.type') }}</h3>
                        </div>
                        <div class="modal-body employeeForm">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    {{ Form::label('type', __('messages.employees.type')) }}
                                    {{ Form::text('type', null, ['class' => 'form-control', 'id' => 'type', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    {{ Form::label('duty_type', __('messages.employees.duty_type')) }}
                                    {{ Form::text('duty_type', null, ['class' => 'form-control', 'id' => 'duty_type', 'autocomplete' => 'off']) }}
                                </div>

                            </div>
                        </div> --}}

                        {{-- Banking starts  here --}}
                        {{-- <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                            <h3>{{ __('messages.employees.salary') }}</h3>
                        </div>
                        <div class="modal-body employeeForm">
                            <div class="row">

                            </div>
                        </div> --}}



                        {{-- <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                            <h3>{{ __('messages.employees.others') }}</h3>
                        </div>
                        <div class="modal-body employeeForm">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('title', __('messages.employees.company_name')) }}
                                    {{ Form::select('company_name', [$company->name => $company->name], $company->name, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                </div>




                                <div class="form-group col-md-3 col-sm-12">
                                    {{ Form::label('street', __('messages.employees.street')) }}
                                    {{ Form::text('street', null, ['class' => 'form-control', 'id' => 'billingStreet', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    {{ Form::label('city', __('messages.employees.city') ) }}
                                    {{ Form::text('city', null, ['class' => 'form-control', 'id' => 'billingCity', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    {{ Form::label('zip', __('messages.employees.zip')) }}
                                    {{ Form::text('zip', null, ['class' => 'form-control', 'id' => 'billingZip', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    {{ Form::label('state', __('messages.employees.state') ) }}
                                    {{ Form::text('state', null, ['class' => 'form-control', 'id' => 'billingState', 'autocomplete' => 'off']) }}

                                </div>
                            </div>

                        </div> --}}

                        <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">

                            <div class="row">
                                <div class="col">
                                    <h3>{{ __('messages.employees.documents') }}</h3>
                                </div>

                                <div class="col">
                                    <button type="button" id="addField" class="btn btn-primary mb-3 float-right"><i
                                            class="fa fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-body employeeForm">

                            <div id="formContainer" class="mb-3">
                                <div class="form-group row mb-3">
                                    <div class="col-md-4">
                                        <label for="doc_name"
                                            class="form-label">{{ __('messages.employees.documents') }}</label>
                                        <input type="text" name="doc_name[]" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="file" class="form-label">{{ __('messages.employees.file_pdf') }},
                                            {{ __('messages.employees.max_size') }}</label>
                                        <input type="file" name="file[]" class="form-control"
                                            accept="application/pdf">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="expiry_date"
                                            class="form-label">{{ __('messages.employees.expiry_date') }}</label>
                                        <input type="date" name="expiry_date[]" class="form-control">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger removeField"
                                            style="display:none;">Remove</button>
                                    </div>
                                </div>
                            </div>



                        </div>


                        <div class="text-right mt-5 mr-1">
                            {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                        </div>


                        {{ Form::close() }}

                    </div>

                </div>
            </div>
        </div>
    @endsection
    @section('page_scripts')
        <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
        <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
        <script src="{{ mix('assets/js/select2.min.js') }}"></script>
        <script src="{{ asset('assets/js/int-tel/js/intlTelInput.min.js') }}"></script>
        <script src="{{ asset('assets/js/int-tel/js/utils.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    @endsection

    @section('scripts')
        <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>

        <script>
            let employeeCreateURL = route('employees.store');
            let employeeURL = route('employees.index') + '/';

            $(document).on('submit', '#addNewForm', function(event) {
                event.preventDefault();
                processingBtn('#addNewForm', '#btnSave', 'loading');
                startLoader();
                // Create a FormData object to handle file uploads
                var formData = new FormData(this);

                $.ajax({
                    url: employeeCreateURL,
                    type: 'POST',
                    data: formData,
                    contentType: false, // Prevent jQuery from setting the content type
                    processData: false, // Prevent jQuery from processing the data
                    success: function(result) {
                        stopLoader();
                        if (result.success) {
                            displaySuccessMessage(result.message);
                            const url = route('employees.index');
                            window.location.href = url;
                        }
                    },
                    error: function(result) {
                        stopLoader();
                        displayErrorMessage(result.responseJSON.message);
                    },
                    complete: function() {
                        stopLoader();
                        processingBtn('#addNewForm', '#btnSave');
                    },
                });
            });
        </script>
        <script>
            $(document).ready(function() {


                $('#departmentSelect').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.employees.select_department') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });
                $('#subDepartmentSelect').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.employees.select_sub_department') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });
                $('#designationSelect').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.employees.select_designation') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });
                $('#selectShift').select2({
                    width: '100%', // Set the width of the select element
                    allowClear: true // Allow clearing the selection
                });
                $('#selectEmpType').select2({
                    width: '100%', // Set the width of the select element
                    allowClear: true // Allow clearing the selection
                });

                $('#selectGender').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.employees.select_gender') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });
                $('#selectBloodGroup').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.employees.select_blood_group') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });
                $('#marital_status').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.employees.select_marital_status') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });

                $('#countryId').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.placeholder.select_country') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });

                $('.datepicker').datetimepicker({
                    format: 'DD-MM-YYYY', // Set the format to dd-mm-yy
                    useCurrent: false, // Disable auto-updating of the field

                });

                var input = document.querySelector("#phone");
                var iti = window.intlTelInput(input, {
                    initialCountry: "ae", // Set default country to Dubai (UAE)
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    nationalMode: false, // Ensure country code is visible and used
                    autoPlaceholder: "off" // Disable the placeholder
                });


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

            });
        </script>

        <script>
            $(document).ready(function() {
                const designations = @json($designations);

                function updateDesignations() {
                    const departmentId = $('#departmentSelect').val(); // Get selected department
                    const filteredDesignations = designations.filter(designation => designation.department_id ==
                        departmentId); // Filter by department_id
                    console.log(filteredDesignations);

                    $('#designationSelect').empty(); // Clear the existing designations
                    $('#designationSelect').append(
                        '<option value="">{{ __('messages.employees.designations') }}</option>'
                    );

                    $.each(filteredDesignations, function(index, designation) {
                        console.log(designation);
                        $('#designationSelect').append(
                            $('<option>', {
                                value: designation.id,
                                text: designation.name
                            })
                        );
                    });
                }

                $('#departmentSelect').on('change', updateDesignations); // Update designations on department change
                updateDesignations(); // Initialize with the current selected department



                $('#departmentSelect').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.employees.select_department') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });
                $('#subDepartmentSelect').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.employees.select_sub_department') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });
                $('#designationSelect').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.employees.select_designation') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });
                $('#selectShift').select2({
                    width: '100%', // Set the width of the select element
                    allowClear: true // Allow clearing the selection
                });

                $('#selectGender').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.employees.select_gender') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });
                $('#selectBloodGroup').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.employees.select_blood_group') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });
                $('#marital_status').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.employees.select_marital_status') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });

                $('#countryId').select2({
                    width: '100%', // Set the width of the select element
                    placeholder: '{{ __('messages.placeholder.select_country') }}', // Placeholder text
                    allowClear: true // Allow clearing the selection
                });






            });
        </script>

        <script>
            $(document).ready(function() {
                $('#image').change(function(e) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(this.files[0]);
                });
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                // Function to toggle the visibility of hourly_rate
                function toggleHourlyRate() {
                    var employmentType = $('#selectEmpType').val();

                    // Show the hourly rate only when "rental" is selected
                    if (employmentType === 'rental') {
                        $('#hourlyRateContainer').show();
                    } else {
                        $('#hourlyRateContainer').hide();
                    }
                }

                // Initial hiding of hourly rate (it is hidden by default in the HTML)
                toggleHourlyRate();

                // Call the function when the employment type dropdown changes
                $('#selectEmpType').change(function() {
                    toggleHourlyRate();
                });
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                // Function to calculate gross salary
                function calculateGrossSalary() {
                    var basicSalary = parseFloat($('#basic_salary').val()) || 0;
                    var transportAllowance = parseFloat($('#transport_allowance').val()) || 0;

                    // Sum of basic salary and transport allowance
                    var grossSalary = basicSalary + transportAllowance;

                    // Set the calculated value to the gross_salary field
                    $('#gross_salary').val(grossSalary.toFixed(2)); // optional: limit to 2 decimal places
                }

                // Event listeners for change in basic_salary and transport_allowance
                $('#basic_salary, #transport_allowance').on('input', function() {
                    calculateGrossSalary();
                });

                // Initial calculation in case there are predefined values
                calculateGrossSalary();
            });
        </script>

        <script>
            $(document).ready(function() {
                $('input, select, textarea').on('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault(); // Prevent form submission on Enter
                        let form = $(this).closest('form');
                        let inputs = form.find('input, select, textarea');
                        let index = inputs.index(this);

                        // Check if the next field is Select2
                        if ($(inputs[index + 1]).hasClass('select2')) {
                            // Listen for Select2 selection
                            $(inputs[index + 1]).on('select2:select', function() {
                                // Focus the next input field after selection
                                inputs.eq(index + 2).focus();
                            });
                        } else if ($(inputs[index + 1]).hasClass('summernote-simple')) {
                            // Focus Summernote editor
                            $(inputs[index + 1]).summernote('focus');
                        } else {
                            // Focus the next input field
                            inputs.eq(index + 1).focus();
                        }
                    }
                });

                // Handle Enter in Select2 field to trigger focus on the next field
                $('.select2').on('select2:select', function(e) {
                    let form = $(this).closest('form');
                    let inputs = form.find('input, select, textarea');
                    let index = inputs.index(this);
                    inputs.eq(index + 1).focus();
                });
            });
        </script>
    @endsection
