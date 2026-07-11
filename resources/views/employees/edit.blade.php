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
            <h1>{{ __('messages.employees.edit_employee') }}</h1>
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

                        {{ Form::open(['id' => 'editForm']) }}
                        {{ Form::hidden('id', $employee->id, ['id' => 'employee_id']) }}

                        {{-- basic info ends here --}}
                        <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;">
                            <h3>{{ __('messages.employees.basic_information') }}</h3>
                        </div>
                        <div class="modal-body employeeForm">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>

                            <div class="row">
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('code', __('messages.employees.code')) }}<span class="required">*</span>
                                    {{ Form::text('code', $employee->code, ['class' => 'form-control', 'required', 'id' => 'code', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    {{ Form::label('title', __('messages.employees.employee_name') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('name', $employee->name, ['class' => 'form-control', 'required', 'id' => 'productUnit_title', 'autocomplete' => 'off', 'placeholder' => __('messages.employees.employee_name')]) }}
                                </div>
                                {{-- {{ $employee->employement_type }}
                                    <div class="form-group col-sm-12 col-md-3">
                                        {{ Form::label('employment_type', __('messages.employees.employment_type') . ':') }}
                                        {{ Form::select('employment_type', ['casual' => 'Casual', 'rental' => 'Rental'], $employee->employment_type, ['class' => 'form-control', 'id' => 'selectEmpType', 'autocomplete' => 'off', 'placeholder' => __('messages.employees.select_employment_type')]) }}
                                    </div> --}}

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('title', __('messages.employees.departments') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('department_id', $departments, $employee->department_id, ['class' => 'form-control', 'required', 'id' => 'departmentSelect', 'autocomplete' => 'off', 'placeholder' => __('messages.employees.departments')]) }}
                                </div>
                                {{-- <div class="form-group col-sm-12 col-md-3">
                                        @php
                                            $newSubDepartment = $subDepartments
                                                ->where('id', $employee->sub_department_id)
                                                ->pluck('name', 'id');
                                        @endphp
                                        {{ Form::label('is_working', __('messages.department.sub_departments') . ':') }}
                                        {{ Form::select('sub_department_id', $newSubDepartment, $employee->sub_department_id, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'subDepartmentSelect', 'placeholder' => __('messages.department.sub_departments')]) }}
                                    </div> --}}

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('title', __('messages.employees.designations') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('designation_id', $designations->pluck('name', 'id'), $employee->designation_id, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'id' => 'designationSelect', 'placeholder' => __('messages.employees.designations')]) }}
                                </div>
                                {{-- <div class="form-group col-sm-12 col-md-3">
                                        {{ Form::label('shift', __('messages.employees.shift') . ':') }}
                                        {{ Form::select('shift_id', $shifts, $employee->shift_id ?? null, ['class' => 'form-control', 'id' => 'selectShift', 'autocomplete' => 'off', 'placeholder' => __('messages.employees.select_shift')]) }}
                                    </div> --}}

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('title', __('messages.employees.phone') . ':') }}
                                    {{ Form::text('phone', $employee->phone, ['class' => 'form-control', 'id' => 'phone', 'autocomplete' => 'off']) }}
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('title', __('messages.employees.email') . ':') }}
                                    {{ Form::email('email', $employee->email, ['class' => 'form-control', 'id' => 'email', 'autocomplete' => 'off']) }}
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('linkedin_url', __('messages.employees.linkedin_url') . ':') }}
                                    {{ Form::text('linkedin_url', $employee->linkedin_url, ['class' => 'form-control', 'id' => 'linkedin_url', 'autocomplete' => 'off']) }}
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('facebook_url', __('messages.employees.facebook_url') . ':') }}
                                    {{ Form::text('facebook_url', $employee->facebook_url, ['class' => 'form-control', 'id' => 'facebook_url', 'autocomplete' => 'off']) }}
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('title', __('messages.employees.joining_date') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::date('join_date', $employee->join_date, ['class' => 'form-control', 'required', 'id' => 'productUnit_title', 'autocomplete' => 'off', 'placeholder' => __('messages.employees.joining_date')]) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('title', __('messages.employees.dob') . ':') }}
                                    {{ Form::date('dob', $employee->dob, ['class' => 'form-control', 'id' => 'productUnit_title', 'autocomplete' => 'off', 'placeholder' => __('messages.employees.dob')]) }}
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('marital_status', __('messages.employees.marital_status') . ':') }}
                                    {{ Form::select('marital_status', ['married' => 'Married', 'unmarried' => 'Unmarried', 'divorced' => 'Divorced'], $employee->marital_status, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'marital_status', 'placeholder' => __('messages.employees.select_marital_status')]) }}
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('blood_group', __('messages.employees.blood_group') . ':') }}
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
                                        $employee->blood_group,
                                        [
                                            'class' => 'form-control',
                                            'id' => 'selectBloodGroup',
                                            'autocomplete' => 'off',
                                            'placeholder' => __('messages.employees.select_blood_group'),
                                        ],
                                    ) }}
                                </div>

                                {{-- <div class="form-group col-sm-12 col-md-3">
                                        {{ Form::label('religion', __('messages.employees.religion') . ':') }}
                                        {{ Form::text('religion', $employee->religion, ['class' => 'form-control', 'id' => 'religion', 'autocomplete' => 'off']) }}
                                    </div> --}}
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('gender', __('messages.employees.gender') . ':') }}
                                    {{ Form::select('gender', ['male' => 'Male', 'female' => 'Female', 'other' => 'Others'], $employee->gender, ['class' => 'form-control', 'id' => 'selectGender', 'autocomplete' => 'off', 'placeholder' => __('messages.employees.select_gender')]) }}
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    {{ Form::label('country', __('messages.employees.country') . ':') }}
                                    {{ Form::select('country', $countries, $employee->country, ['id' => 'countryId', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_country')]) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('is_working', __('messages.employees.status') . ':') }}
                                    {{ Form::select('status', ['1' => 'Active', '0' => 'Inactive'], $employee->status, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('basic_salary', __('messages.employees.basic_salary') . ':') }}
                                    {{ Form::number('basic_salary', $employee->basic_salary, ['class' => 'form-control', 'id' => 'basic_salary', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('currency_id', __('messages.employees.currency') . ':') }}
                                    {{ Form::select('currency_id', $currencies, $employee->currency_id, ['class' => 'form-control', 'id' => 'currency_id', 'placeholder' => 'Select Currency']) }}
                                </div>
                                {{-- <div class="form-group col-sm-12 col-md-3">
                                        {{ Form::label('transport_allowance', __('messages.employees.transport_allowance') . ':') }}
                                        {{ Form::number('transport_allowance', $employee->transport_allowance, ['class' => 'form-control', 'id' => 'transport_allowance', 'autocomplete' => 'off']) }}
                                    </div> --}}
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('gross_salary', __('messages.employees.gross_salary')) }}
                                    {{ Form::number('gross_salary', null, ['class' => 'form-control', 'id' => 'gross_salary', 'autocomplete' => 'off', 'readonly' => true]) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-3" id="hourlyRateContainer" style="display: none;">
                                    {{ Form::label('hourly_rate', __('messages.employees.hourly_rate') . ':') }}
                                    {{ Form::number('hourly_rate', $employee->hourly_rate, ['class' => 'form-control', 'id' => 'hourly_rate', 'autocomplete' => 'off']) }}
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('hourly_rate', __('messages.branches.name')) }}
                                    {{ Form::select('branch_id', $usersBranches ?? [], $employee->branch_id ?? null, ['class' => 'form-control select2']) }}
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('hourly_rate', __('messages.employees.absent_allowance_deduction')) }}
                                    {{ Form::number('absent_allowance_deduction', $employee->absent_allowance_deduction ?? 0, ['class' => 'form-control', 'step' => 'any']) }}
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
                                    {{ Form::text('iqama_no', $employee->iqama_no ?? null, ['class' => 'form-control', 'required', 'id' => 'iqama_no', 'autocomplete' => 'off']) }}

                                    {{ Form::label('iqama_no_expiry_date', __('messages.employees.iqama_no_expiry_date'), ['class' => 'mt-3']) }}
                                    {{ Form::text('iqama_no_expiry_date', $employee->iqama_no_expiry_date ? \Carbon\Carbon::parse($employee->iqama_no_expiry_date)->format('d-m-Y') : null, ['class' => 'form-control datepicker', 'autocomplete' => 'off']) }}
                                </div>

                                <!-- Second Column: Passport Number and Expiry Date -->
                                <div class="form-group col-sm-12 col-md-4">
                                    {{ Form::label('passport', __('messages.employees.passport')) }}
                                    {{ Form::text('passport', $employee->passport ?? null, ['class' => 'form-control', 'autocomplete' => 'off']) }}

                                    {{ Form::label('passport_expiry_date', __('messages.employees.passport_expiry_date'), ['class' => 'mt-3']) }}
                                    {{ Form::text('passport_expiry_date', $employee->passport_expiry_date ? \Carbon\Carbon::parse($employee->passport_expiry_date)->format('d-m-Y') : null, ['class' => 'form-control datepicker', 'autocomplete' => 'off']) }}
                                </div>

                                <!-- Third Column: TUV Number and Expiry Date -->
                                {{-- <div class="form-group col-sm-12 col-md-3">
                                        {{ Form::label('tuv_no', __('messages.employees.tuv_no')) }}
                                        {{ Form::text('tuv_no', $employee->tuv_no ?? null, ['class' => 'form-control', 'autocomplete' => 'off']) }}

                                        {{ Form::label('tuv_no_expiry_date', __('messages.employees.tuv_no_expiry_date'), ['class' => 'mt-3']) }}
                                        {{ Form::date('tuv_no_expiry_date', $employee->tuv_no_expiry_date ?? null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                    </div> --}}

                                <!-- Fourth Column: Driving License Number and Expiry Date -->
                                <div class="form-group col-sm-12 col-md-4">
                                    {{ Form::label('driving_license_no', __('messages.employees.driving_license_no')) }}
                                    {{ Form::text('driving_license_no', $employee->driving_license_no ?? null, ['class' => 'form-control', 'autocomplete' => 'off']) }}

                                    {{ Form::label('driving_license_expiry_date', __('messages.employees.driving_license_expiry_date'), ['class' => 'mt-3']) }}
                                    {{ Form::text('driving_license_expiry_date', $employee->driving_license_expiry_date ? \Carbon\Carbon::parse($employee->driving_license_expiry_date)->format('d-m-Y') : null, ['class' => 'form-control datepicker', 'autocomplete' => 'off']) }}
                                </div>

                            </div>
                        </div>

                        <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                            <h3>{{ __('messages.employees.bank') }}</h3>
                        </div>
                        <div class="modal-body employeeForm">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    {{ Form::label('bank_name', __('messages.employees.bank_name') . ':') }}
                                    {{ Form::text('bank_name', $employee->bank_name, ['class' => 'form-control', 'id' => 'bank_name', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    {{ Form::label('branch_name', __('messages.employees.branch_name') . ':') }}
                                    {{ Form::text('branch_name', $employee->branch_name, ['class' => 'form-control', 'id' => 'branch_name', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    {{ Form::label('bank_account_no', __('messages.employees.bank_account_no') . ':') }}
                                    {{ Form::text('bank_account_no', $employee->bank_account_no, ['class' => 'form-control', 'id' => 'bank_account_no', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    {{ Form::label('iban_num', __('messages.employees.iban_num') . ':') }}
                                    {{ Form::text('iban_num', $employee->iban_num, ['class' => 'form-control', 'id' => 'iban_num', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 col-md-12">
                                    {{ Form::label('router_number', __('messages.employees.router_number') . ':') }}
                                    {{ Form::text('router_number', $employee->router_number, ['class' => 'form-control', 'id' => 'router_number', 'autocomplete' => 'off']) }}
                                </div>
                            </div>
                        </div>




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
                                    <img id="imagePreview"
                                        src="{{ $employee->image ? asset('uploads/public/employee_images/' . $employee->image) : '' }}"
                                        alt="Image Preview"
                                        style="display: {{ $employee->image ? 'block' : 'none' }}; max-width: 50%; height: auto; border-radius: 5px;" />
                                </div>
                            </div>
                        </div>

                        {{-- <div
                                style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                                <h3>{{ __('messages.employees.type') }}</h3>
                            </div>
                            <div class="modal-body employeeForm">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4">
                                        {{ Form::label('type', __('messages.employees.type') . ':') }}
                                        {{ Form::text('type', $employee->type, ['class' => 'form-control', 'id' => 'type', 'autocomplete' => 'off']) }}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-4">
                                        {{ Form::label('duty_type', __('messages.employees.duty_type') . ':') }}
                                        {{ Form::text('duty_type', $employee->duty_type, ['class' => 'form-control', 'id' => 'duty_type', 'autocomplete' => 'off']) }}
                                    </div>

                                </div> --}}


                        {{-- Banking starts  here --}}
                        {{-- <div
                                style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                                <h3>{{ __('messages.employees.salary') }}</h3>
                            </div>
                            <div class="modal-body employeeForm">
                                <div class="row">

                                </div>
                            </div> --}}

                        {{-- Banking starts  here --}}

                        {{--  --}}

                        {{-- <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                            <h3>{{ __('messages.employees.others') }}</h3>
                        </div>
                        <div class="modal-body employeeForm">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-3">
                                    {{ Form::label('title', __('messages.employees.company_name') . ':') }}
                                    {{ Form::select('company_name', [$company->name => $company->name], $company->name, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                </div>




                                <div class="form-group col-md-3 col-sm-12">
                                    {{ Form::label('street', __('messages.employees.street') . ':') }}
                                    {{ Form::text('street', $employee->street, ['class' => 'form-control', 'id' => 'billingStreet', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                        {{ Form::label('city', __('messages.employees.city') . ':') }}
                                        {{ Form::text('city', $employee->city, ['class' => 'form-control', 'id' => 'billingCity', 'autocomplete' => 'off']) }}
                                    </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    {{ Form::label('zip', __('messages.employees.zip') . ':') }}
                                    {{ Form::text('zip', $employee->zip, ['class' => 'form-control', 'id' => 'billingZip', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                        {{ Form::label('state', __('messages.employees.state') . ':') }}
                                        {{ Form::text('state', $employee->state, ['class' => 'form-control', 'id' => 'billingState', 'autocomplete' => 'off']) }}

                                    </div>
                            </div>

                        </div> --}}


                        <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                            <h3>{{ __('messages.employees.manage_documents') }}</h3>
                        </div>
                        <div class="modal-body employeeForm">
                            <div class="row">

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{ __('messages.employees.documents') }}</th>
                                            <th scope="col">{{ __('messages.employees.files') }}</th>
                                            <th scope="col">{{ __('messages.employees.expiry_date') }}</th>
                                            <th scope="col">{{ __('messages.common.action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employee->documents as $document)
                                            <tr>
                                                <td>{{ $document->name }}</td>
                                                <td>{{ $document->file }}</td>
                                                <td>{{ $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('d/m/Y') : 'N/A' }}
                                                </td>
                                                {{-- <td>
                                                    <a href="{{ asset('uploads/public/employee_docs/' . basename($document->file)) }}"
                                                        class="btn btn-primary" target="_blank" title="View PDF">
                                                        <i class="fas fa-eye"></i>

                                                    </a>
                                                    <button type="button" class="btn btn-danger fileDeleteBtn"
                                                        data-toggle="modal" data-id="{{ $document->id }}"
                                                        title="Delete Document">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td> --}}
                                                <td>
                                                    <a href="{{ asset('uploads/public/employee_docs/' . basename($document->file)) }}"
                                                        class="btn btn-primary" target="_blank" title="View PDF"
                                                        style="width: 40px !important; height: 32px !important; padding: 0 !important; margin: 0 5px 0 0 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; box-sizing: border-box !important; border-width: 1px !important; min-width: 40px !important; max-width: 40px !important;">
                                                        <i class="fas fa-eye"
                                                            style="font-size: 14px !important; line-height: 1 !important; margin: 0 !important; padding: 0 !important;"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger fileDeleteBtn"
                                                        data-toggle="modal" data-id="{{ $document->id }}"
                                                        title="Delete Document"
                                                        style="width: 40px !important; height: 32px !important; padding: 0 !important; margin: 0 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; box-sizing: border-box !important; border-width: 1px !important; min-width: 40px !important; max-width: 40px !important;">
                                                        <i class="fas fa-trash-alt"
                                                            style="color: #fff !important; font-size: 14px !important; line-height: 1 !important; margin: 0 !important; padding: 0 !important;">
                                                        </i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div style="background:;width:100%;border-radius:5px; color:#6777ef;height:50px;margin-top:15px;">
                            <div class="row">
                                <div class="col">
                                    <h3>{{ __('messages.employees.upload_documents') }}</h3>
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
                                        <label for="doc_name" class="form-label">Name:</label>
                                        <input type="text" name="doc_name[]" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="file" class="form-label">{{ __('messages.employees.file_pdf') }},
                                            {{ __('messages.employees.max_size') }}</label>
                                        <input type="file" name="file[]" class="form-control"
                                            accept="application/pdf">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="expiry_date" class="form-label">Expiry Date:</label>
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
            $(document).on('submit', '#editForm', function(event) {
                event.preventDefault();
                startLoader();
                processingBtn('#editForm', '#btnSave', 'loading');
                let id = $('#employee_id').val();
                var formData = new FormData(this);
                $.ajax({
                    url: route('employees.update', id),
                    type: 'POST',
                    data: formData,
                    contentType: false, // Prevent jQuery from setting the content type
                    processData: false, // Prevent jQuery from processing the data
                    success: function(result) {
                        stopLoader();
                        if (result.success) {
                            displaySuccessMessage(result.message);
                            $('.modal').modal('hide');
                            const url = route('employees.index', );
                            window.location.href = url;

                        }
                    },
                    error: function(result) {
                        stopLoader();
                        displayErrorMessage(result.responseJSON.message);
                    },
                    complete: function() {
                        processingBtn('#editForm', '#btnSave');
                    },
                });
            });


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

            $(document).on('click', '.fileDeleteBtn', function(event) {
                let fileId = $(event.currentTarget).data('id');
                console.log(route('employees.file.delete', fileId));

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
                        deleteItemAjax(route('employees.file.delete', fileId),
                            "{{ __('messages.employees.file') }}", row, callFunction = null);
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

                // Initialize JSON data
                const designations = @json($designations);

                // Function to update designations based on the selected department
                function updateDesignations() {
                    const departmentId = $('#departmentSelect').val(); // Get selected department ID
                    const filteredDesignations = designations.filter(designation => designation.department_id ==
                        departmentId); // Filter designations based on department_id

                    $('#designationSelect').empty(); // Clear existing designations
                    $('#designationSelect').append(
                        '<option value="">{{ __('messages.employees.designations') }}</option>'
                    );

                    // Append filtered designations to the dropdown
                    $.each(filteredDesignations, function(index, designation) {
                        $('#designationSelect').append(
                            $('<option>', {
                                value: designation.id,
                                text: designation.name
                            })
                        );
                    });
                }
                // Event listener for department change
                $('#departmentSelect').on('change', updateDesignations);

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
