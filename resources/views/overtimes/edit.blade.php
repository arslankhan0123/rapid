@extends('layouts.app')
@section('title')
    {{ __('messages.overtimes.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.overtimes.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('overtimes.index') }}" class="btn btn-primary form-btn">List</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="modal-content">
                        {{ Form::open(['id' => 'editForm']) }}
                        {{ Form::hidden('id', $overtime->id, ['id' => 'bonus_id']) }}
                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    {{ Form::label('employee_id', __('messages.overtimes.select_iqama') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select(
                                        'employee_id',
                                        $employees->mapWithKeys(function ($employee) {
                                            return [$employee->id => $employee->iqama_no . ' (' . $employee->name . ')'];
                                        }),
                                        $overtime->employee_id ?? null,
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
                                            <p id="employeeSubDepartment" class="text-black"></p>
                                            <p id="employeeDepartment" class="text-black"></p>
                                        </div>
                                        <div class="col-md-3 col-sm-12 image_preview">
                                            <img id="employeeImage" src="" alt="Employee Image"
                                                style="display: none; max-width: 200px; height: auto; border-radius: 5px;" />
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('overtime_type_id', __('messages.overtimes.type') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('overtime_type_id', $types, $overtime->overtime_type_id, [
                                        'class' => 'form-control',
                                        'required',
                                        'id' => 'type_select',
                                        'placeholder' => __('messages.bonuses.type'),
                                    ]) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('title', __('messages.overtimes.amount') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::number('amount', $overtime->amount, ['class' => 'form-control', 'required', 'id' => 'bonus_name', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12 mb-0">
                                    {{ Form::label('description', __('messages.overtimes.description') . ':') }}
                                    {{ Form::textarea('description', $overtime->description, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
                                </div>
                            </div>
                            <div class="text-right mr-1">
                                {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
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
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        'use strict';


        $(document).on('submit', '#editForm', function(event) {
            event.preventDefault();
            processingBtn('#editForm', '#btnSave', 'loading');
            let id = $('#bonus_id').val();

            let description = $('<div />').
            html($('#editCategoryDescription').summernote('code'));
            let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

            if ($('#editCategoryDescription').summernote('isEmpty')) {
                $('#editCategoryDescription').val('');
            } else if (empty) {
                displayErrorMessage(
                    'Description field is not contain only white space');
                processingBtn('#addNewForm', '#btnSave', 'reset');
                return false;
            }

            $.ajax({
                url: route('overtimes.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('overtimes.index', );
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#editForm', '#btnSave');
                },
            });
        });
    </script>
        <script>
        $(document).ready(function() {
            $('#employee_select').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#type_select').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            // Cancel button
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

                    // Update employee details
                    $('#employeeName').text('{{ __('messages.allowances.employee_name') }} : ' + employee
                        .name);
                    $('#employeeDepartment').text(
                        '{{ __('messages.allowances.employee_department') }} : ' + employee
                        .department.name);
                    $('#employeeSubDepartment').text(
                        '{{ __('messages.allowances.employee_sub_department') }} : ' + employee
                        .sub_department.name);
                    $('#employeeDesignation').text(
                        '{{ __('messages.allowances.employee_designation') }} : ' + (
                            employee.designation ? employee
                            .designation.name : 'N/A'));
                } else {
                    $('#employeeImage').hide();
                    $('#employeeName').text('');
                    $('#employeeDesignation').text('');
                    $('#employeeSubDepartment').text('');
                    $('#employeeDepartment').text('');
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
                    $('#employeeDesignation').text('Designation: ' + (defaultEmployee.designation ? defaultEmployee
                        .designation.name : 'N/A'));
                    $('#employeeDepartment').text(
                        '{{ __('messages.allowances.employee_department') }} : ' + defaultEmployee
                        .department.name);
                    $('#employeeSubDepartment').text(
                        '{{ __('messages.allowances.employee_sub_department') }} : ' + defaultEmployee
                        .sub_department.name);
                }
            }
        });
    </script>
@endsection
