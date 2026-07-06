@extends('layouts.app')
@section('title')
    {{ __('messages.terminations.terminations') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <style>
        #statusSwitch.form-check-input {
            width: 3em;
            height: 1.5em;
        }

        #statusSwitch.form-check-input:checked {
            background-color: #0d6efd;
            /* Change the color as needed */
        }

        #statusSwitch.form-check-input::before {
            width: 1.5em;
            height: 1.5em;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.terminations.add') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('terminations.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.terminations.list') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                {{ Form::open(['id' => 'addNewFormDepartmentNew']) }}
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">

                        <div class="form-group col-sm-12">
                            {{ Form::label('approved_date', __('messages.common.date') . ':') }}<span
                                class="required">*</span>
                            {{ Form::date('date', null, ['class' => 'form-control', 'required']) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('employee_id', __('messages.overtimes.select_iqama') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select(
                                'employee_id',
                                $employees->mapWithKeys(function ($employee) {
                                    return [$employee->id => $employee->iqama_no . ' (' . $employee->name . ')'];
                                }),
                                null,
                                [
                                    'class' => 'form-control',
                                    'required',
                                    'id' => 'employee_select',
                                    'placeholder' => __('messages.overtimes.select_iqama'),
                                ],
                            ) }}
                        </div>
                        <div class="form-group col-sm-12 ">
                            <div class="row">
                                <div class="col-md-4">
                                    <p id="employeeName" class="text-black"></p>
                                    <p id="employeeDesignation" class="text-black"></p>
                                    {{-- <p id="employeeSubDepartment" class="text-black"></p> --}}
                                    <p id="employeeDepartment" class="text-black"></p>
                                </div>
                                <div class="col-md-3 col-sm-12 image_preview">
                                    <img id="employeeImage" src="" alt="Employee Image"
                                        style="display: none; max-width: 200px; height: auto; border-radius: 5px;" />
                                </div>

                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('bonus_type_id', __('messages.branches.name') . ':') }}<span
                                class="required"></span>
                            {{ Form::select('from', $usersBranches, null, [
                                'class' => 'form-control',
                                'id' => 'from_branch',
                                'style' => ' pointer-events: none; background-color: #e9ecef;',
                                'placeholder' => __('messages.branches.name'),
                            ]) }}
                        </div>
                        {{-- <div class="form-group col-md-6">
                            {{ Form::label('name', __('messages.terminations.name') . ':') }}<span
                                class="required">*</span>
                            {{ Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'designation_name', 'autocomplete' => 'off']) }}
                        </div> --}}
                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('description', __('messages.terminations.description') . ':') }}
                            {{ Form::textarea('description', null, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
                        </div>

                    </div>

                    <div class="form-group col-sm-12 mb-0">
                        <div class="form-check form-switch ">
                            {{ Form::label('status', __('messages.terminations.status') . ':') }}
                            {{ Form::checkbox('status', 1, false, ['class' => 'form-check-input ml-4', 'id' => 'statusSwitch']) }}
                        </div>
                    </div>
                    <div class="text-right mr-1">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        let departmentNewCreateUrl = route('terminations.store');
        $(document).on('submit', '#addNewFormDepartmentNew', function(event) {
            event.preventDefault();
            processingBtn('#addNewFormDepartmentNew', '#btnSave', 'loading');

            let description = $('<div />').
            html($('#createDescription').summernote('code'));
            let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

            if ($('#createDescription').summernote('isEmpty')) {
                $('#createDescription').val('');
            } else if (empty) {
                displayErrorMessage(
                    'Description field is not contain only white space');
                processingBtn('#addNewFormDepartmentNew', '#btnSave', 'reset');
                return false;
            }
            $.ajax({
                url: departmentNewCreateUrl,
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('terminations.index', );
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

            $('#employee_select').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.terminations.select_employee') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Employee data stored in a JavaScript object
            var employees = @json($employees);

            $('#employee_select').change(function() {
                var selectedEmployeeId = $(this).val();
                var employee = employees.find(emp => emp.id == selectedEmployeeId);

                if (employee) {
                    // Update image preview
                    if (employee.image) {
                        var imageUrl = "{{ asset('uploads/public/employee_images/') }}/" + employee.image;
                        $('#employeeImage').attr('src', imageUrl).show();
                    } else {
                        $('#employeeImage').hide();
                    }

                    if (employee.branch?.id) {
                        $("#from_branch").val(employee.branch.id);
                    } else {
                        $("#from_branch").val('');
                    }
                    // Update employee details
                    $('#employeeName').text('{{ __('messages.allowances.employee_name') }} : ' + employee
                        .name);
                    $('#employeeDepartment').text(
                        '{{ __('messages.allowances.employee_department') }} : ' + employee
                        .department.name);
                    // $('#employeeSubDepartment').text(
                    //     '{{ __('messages.allowances.employee_sub_department') }} : ' + employee
                    //     .sub_department.name);
                    $('#employeeDesignation').text(
                        '{{ __('messages.allowances.employee_designation') }} : ' + (
                            employee.designation ? employee
                            .designation.name : 'N/A'));
                } else {
                    $('#employeeImage').hide();
                    $('#employeeName').text('');
                    $('#employeeDepartment').text('');
                    // $('#employeeSubDepartment').text('');
                    $('#employeeDesignation').text('');
                }
            });

            // Initialize with default selected employee details if available
            var defaultEmployeeId = $('#employee_select').val();
            if (defaultEmployeeId) {
                var defaultEmployee = employees.find(emp => emp.id == defaultEmployeeId);
                if (defaultEmployee) {
                    // Update image preview
                    if (defaultEmployee.image) {
                        var imageUrl = "{{ asset('uploads/public/employee_images/') }}/" + defaultEmployee.image;
                        $('#employeeImage').attr('src', imageUrl).show();
                    }

                    // Update employee details
                    $('#employeeName').text('Name: ' + defaultEmployee.name);
                    $('#employeeEmail').text('Email: ' + defaultEmployee.email);
                    $('#employeeDesignation').text('Designation: ' + (defaultEmployee.designation ? defaultEmployee
                        .designation.name : 'N/A'));
                }
            }
        });
    </script>
@endsection
