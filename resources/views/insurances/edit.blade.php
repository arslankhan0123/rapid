@extends('layouts.app')
@section('title')
    {{ __('messages.insurances.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>GOSI insurance Edit</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('insurances.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.insurances.list') }}</a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    {{ Form::open(['id' => 'editForm']) }}
                    {{ Form::hidden('id', $insurance->id, ['id' => 'commission_id']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        
                        <div class="form-group col-sm-12">
                            {{ Form::label('description', __('messages.allowances.date') . ':') }}
                            {{ Form::date('date', $insurance->date, ['class' => 'form-control', 'id' => 'insuranceDate']) }}
                        </div>

                        {{-- <div class="form-group col-sm-12 ">
                                    {{ Form::label('description', __('messages.insurances.employee_number') . ':') }}
                                    {{ Form::text('employee_number', $employeeData->id, ['class' => 'form-control', 'id' => 'employee_number', 'disabled']) }}
                                </div> --}}
                        <div class="form-group col-sm-12">
                            {{ Form::label('employee_id', __('messages.attendances.select_iqama') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select(
                                'employee_id',
                                $employees->mapWithKeys(function ($employee) {
                                    return [$employee->id => $employee->iqama_no . ' (' . $employee->name . ')'];
                                }),
                                $insurance->employee_id ?? null,
                                [
                                    'class' => 'form-control',
                                    'required',
                                    'id' => 'employee_select',
                                    'placeholder' => __('messages.attendances.select_iqama'),
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

                        {{-- <div class="form-group col-sm-12 ">
                                    {{ Form::label('description', __('messages.insurances.department') . ':') }}
                                    {{ Form::text('department', $employeeData->department->name, ['class' => 'form-control', 'id' => 'department', 'disabled']) }}
                                </div>
                                <div class="form-group col-sm-12 ">
                                    {{ Form::label('description', __('messages.insurances.sub_department') . ':') }}
                                    {{ Form::text('sub_department', $employeeData->subDepartment->name, ['class' => 'form-control', 'id' => 'sub_department', 'disabled']) }}
                                </div>
                                <div class="form-group col-sm-12 ">
                                    {{ Form::label('description', __('messages.insurances.designation') . ':') }}
                                    {{ Form::text('designation', $employeeData->designation->name, ['class' => 'form-control', 'id' => 'designation', 'disabled']) }}
                                </div> --}}

                        <div class="form-group col-md-6">
                            {{ Form::label('bonus_type_id', __('messages.branches.name') . ':') }}<span
                                class="required"></span>
                            {{ Form::select('from', $usersBranches, $insurance->employee?->branch_id ?? null, [
                                'class' => 'form-control',
                                'id' => 'from_branch',
                                'style' => ' pointer-events: none; background-color: #e9ecef;',
                                'placeholder' => __('messages.branches.name'),
                            ]) }}
                        </div>
                        
                        <div class="form-group col-sm-12">
                            {{ Form::label('insurance_type', __('messages.insurances.insurance_type') . ':') }}<span class="required">*</span>
                            {{ Form::text('insurance_type', $insurance->insurance_type, ['class' => 'form-control', 'required', 'id' => 'insurance_type' , 'readonly']) }}
                        </div>
                        
                        <!--<div class="form-group col-sm-12">-->
                        <!--    {{ Form::label('monthly_insurance', __('messages.insurances.monthly_insurance') . ':') }}<span class="required">*</span>-->
                        <!--    {{ Form::number('monthly_insurance', $insurance->monthly_insurance, ['class' => 'form-control', 'required', 'id' => 'monthly_insurance', 'step' => 'any']) }}-->
                        <!--</div>-->
    
                        
                        <!--<div class="form-group col-sm-12">-->
                        <!--    {{ Form::label('hourly_insurance', __('messages.insurances.hourly_insurance') . ':') }}<span class="required">*</span>-->
                        <!--    {{ Form::number('hourly_insurance', $insurance->hourly_insurance, ['class' => 'form-control', 'required', 'id' => 'hourly_insurance', 'step' => 'any', 'readonly']) }}-->
                        <!--</div>-->
    
                        
                        <!--<div class="form-group col-sm-12">-->
                        <!--    {{ Form::label('worked_hours', __('messages.insurances.worked_hours') . ':') }}<span class="required">*</span>-->
                        <!--    {{ Form::number('worked_hours', $insurance->worked_hours, ['class' => 'form-control', 'required', 'id' => 'worked_hours', 'step' => 'any', 'readonly']) }}-->
                        <!--</div>-->
                        
                        <div class="form-group col-md-6">
                            {{ Form::label('insurance', 'Net ' . __('messages.insurances.name') . ':') }}
                            {{ Form::number('insurance', $insurance->insurance, ['class' => 'form-control', 'required', 'id' => 'insurance']) }}
                        </div>

                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('description', __('messages.insurances.description') . ':') }}
                            {{ Form::textarea('description', $insurance->description, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
                        </div>
                    </div>

                    <div class="text-right mr-1">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

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
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        'use strict';
        $(document).on('submit', '#editForm', function(event) {
            event.preventDefault();
            processingBtn('#editForm', '#btnSave', 'loading');
            let id = $('#commission_id').val();
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
            var departmentSelect = $('#departmentSelect').val();
            if (departmentSelect === '' || departmentSelect === null) {
                displayErrorMessage('{{ __('messages.insurances.select_department') }}');
                return false;
            }
            $.ajax({
                url: route('insurances.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('insurances.index', );
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

        $(document).ready(function() {
            $('#employee_select').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.insurances.select_employee') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });


            $('#employee_select').on('change', function() {
                var employeeId = $(this).val();
                // Check if an employee ID is selected
                if (employeeId) {
                    $.ajax({
                        url: route('insurances.employee.info',
                            employeeId), // Dynamically append the employee ID
                        type: 'GET',
                        success: function(response) {
                            if (response) {
                                $('#employee_number').val(response.id);
                                $('#department').val(response.department ? response.department
                                    .name : '');
                                $('#sub_department').val(response.sub_department ? response
                                    .sub_department.name : '');
                                $('#designation').val(response.designation ? response
                                    .designation.name : '');
                            }
                            // You can update your form fields or do other DOM manipulations here
                        },
                        error: function(xhr, status, error) {
                            // Handle errors here
                            console.error(error);
                        }
                    });
                }
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
                    $('#employeeDesignation').text('');
                    // $('#employeeSubDepartment').text('');
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
                    // $('#employeeSubDepartment').text(
                    //     '{{ __('messages.allowances.employee_sub_department') }} : ' + defaultEmployee
                    //     .sub_department.name);
                }
            }
        });
    </script>
    
    <script>
        // Auto Calculate Hourly Insurance & Total
        $('#monthly_insurance').on('keyup change', function() {
            let monthlyInsurance = parseFloat($(this).val());
            let employeeId = $('#employee_select').val();
            let issueDate = $('#insuranceDate').val(); // Add date field if needed
    
            if (!employeeId || isNaN(monthlyInsurance) || monthlyInsurance <= 0) return;
    
            $.ajax({
                url: `https://qc.smitsmakkah.com/admin/allowances/get-employee-working-days/${employeeId}/${issueDate}`,
                type: 'GET',
                success: function(response) {
                    if (response.total_worked && response.ideal_worked) {
                        let totalWorked = parseFloat(response.total_worked);
                        let idealWorked = parseFloat(response.ideal_worked);
    
                        let hourly = monthlyInsurance / idealWorked;
                        let total = hourly * totalWorked;
    
                        $('#hourly_insurance').val(hourly.toFixed(2));
                        $('#worked_hours').val(totalWorked);
                        $('#insurance').val(total.toFixed(2));
                    }
                }
            });
        });
    </script>
@endsection
