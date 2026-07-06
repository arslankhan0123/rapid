@extends('layouts.app')
@section('title')
    {{ __('messages.allowances.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.allowances.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('allowances.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.allowances.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    {{ Form::open(['id' => 'editForm']) }}
                    {{ Form::hidden('id', $allowance->id, ['id' => 'allowance_id']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            {{ Form::label('description', __('messages.allowances.date') . ':') }}
                            {{ Form::date('date', $allowance->date, ['class' => 'form-control', 'id' => 'allowanceDate']) }}
                        </div>

                        <div class="form-group col-sm-12">
                            {{ Form::label('employee_id', __('messages.attendances.select_iqama') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select(
                                'employee_id',
                                $employees->mapWithKeys(function ($employee) {
                                    return [$employee->id => $employee->iqama_no . ' (' . $employee->name . ')'];
                                }),
                                $allowance->employee_id ?? null,
                                [
                                    'class' => 'form-control',
                                    'required',
                                    'id' => 'employee_select',
                                    'placeholder' => __('messages.attendances.select_iqama'),
                                ],
                            ) }}
                        </div>

                        <div class="form-group ml-1 col-sm-12 ">
                            <div class="row">
                                <div class="col-md-4 text-dark">
                                    <p id="employeeName" class="text-dark"></p>
                                    <p id="employeeDesignation" class="text-dark"></p>
                                    {{-- <p id="employeeSubDepartment" class="text-dark"></p>
                                            <p id="employeeDepartment" class="text-dark"></p> --}}

                                </div>
                                <div class="col-md-3 col-sm-12 image_preview">
                                    <img id="employeeImage" src="" alt="Employee Image"
                                        style="display: none; max-width: 250px; height: auto; border-radius: 5px;" />
                                </div>

                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('bonus_type_id', __('messages.transfers.from') . ':') }}<span
                                class="required"></span>
                            {{ Form::select('from', $usersBranches, $allowance->employee?->branch_id ?? null, [
                                'class' => 'form-control',
                                'id' => 'from_branch',
                                'style' => ' pointer-events: none; background-color: #e9ecef;',
                                'placeholder' => __('messages.branches.name'),
                            ]) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('allowance_type_id', __('messages.allowances.type') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('allowance_type_id', $types, $allowance->allowance_type_id, [
                                'class' => 'form-control',
                                'required',
                                'id' => 'type_select',
                                'placeholder' => __('messages.allowances.type'),
                            ]) }}
                        </div>
                        {{-- <div class="form-group col-sm-12">
                            {{ Form::label('allowance_type_id', __('messages.allowances.payment_type') . ':') }}
                            {{ Form::select('payment_type', $payment_types ?? [], $allowance->payment_type, [
                                'class' => 'form-control',
                                'required',
                                'placeholder' => __('messages.allowances.payment_type'),
                            ]) }}
                        </div> --}}
                        
                        <!--<div class="form-group col-sm-12">-->
                        <!--    {{ Form::label('title', __('messages.allowances.monthly_allowance') . ':') }}<span-->
                        <!--        class="required">*</span>-->
                        <!--    {{ Form::number('monthly_allowance', $allowance->monthly_allowance, ['class' => 'form-control', 'required', 'id' => 'monthly_allowance', 'autocomplete' => 'off']) }}-->
                        <!--</div>-->
                        <!--<div class="form-group col-sm-12">-->
                        <!--    {{ Form::label('title', __('messages.allowances.hourly_allowance') . ':') }}<span-->
                        <!--        class="required">*</span>-->
                        <!--    {{ Form::number('hourly_allowance', $allowance->hourly_allowance, ['class' => 'form-control', 'required', 'id' => 'hourly_allowance', 'autocomplete' => 'off' , 'step' => 'any']) }}-->
                        <!--</div>-->
                        <!--<div class="form-group col-sm-12">-->
                        <!--    {{ Form::label('title', __('messages.allowances.worked_hours') . ':') }}<span-->
                        <!--        class="required">*</span>-->
                        <!--    {{ Form::number('worked_hours', $allowance->worked_hours, ['class' => 'form-control', 'required', 'id' => 'worked_hours', 'autocomplete' => 'off' , 'step' => 'any']) }}-->
                        <!--</div>-->
                        
                        <div class="form-group col-sm-12">
                            {{ Form::label('title', __('messages.allowances.amount') . ':') }}<span
                                class="required">*</span>
                            {{ Form::number('amount', $allowance->amount, ['class' => 'form-control', 'required', 'id' => 'bonus_name', 'autocomplete' => 'off' , 'step' => 'any']) }}
                        </div>
                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('description', __('messages.allowances.description') . ':') }}
                            {{ Form::textarea('description', $allowance->description, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
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
            let id = $('#allowance_id').val();

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
                url: route('allowances.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('allowances.index', );
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

                    if (employee.branch?.id) {
                        $("#from_branch").val(employee.branch.id);
                    } else {
                        $("#from_branch").val('');
                    }

                    $('#employeeDesignation').text(
                        '{{ __('messages.allowances.employee_designation') }} : ' + (
                            employee.designation ? employee
                            .designation.name : 'N/A'));
                } else {
                    $('#employeeImage').hide();
                    $('#employeeName').text('');

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
                    $('#employeeName').text('{{ __('messages.allowances.employee_name') }} : ' + defaultEmployee
                        .name);

                    $('#employeeDesignation').text('Designation: ' + (defaultEmployee.designation ? defaultEmployee
                        .designation.name : 'N/A'));
                }
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            const allEmployees = @json($employees);
            const savedEmployeeId = @json($allowance->employee_id);

            $('#allowanceDate').on('change', function() {
                const selectedDate = $(this).val();
                const $employeeSelect = $('#employee_select');
                const currentSelected = $employeeSelect.val() || savedEmployeeId;

                // Clear existing options
                $employeeSelect.empty();

                // Add placeholder
                $employeeSelect.append(`<option value="">${@json(__('messages.attendances.select_iqama'))}</option>`);

                // Append all employees without filtering by join_date in edit case
                allEmployees.forEach(employee => {
                    const optionText = `${employee.iqama_no} (${employee.name})`;
                    const option = $('<option>', {
                        value: employee.id,
                        text: optionText
                    });
                    if (employee.id == currentSelected) {
                        option.attr('selected', 'selected');
                    }
                    $employeeSelect.append(option);
                });

                // Trigger change to update Select2 and trigger employee detail fetch script
                $employeeSelect.trigger('change');
            });

            // Trigger once if date is already filled
            if ($('#allowanceDate').val()) {
                $('#allowanceDate').trigger('change');
            }
        });
    </script>
    <script>
        // $(document).ready(function() {
        //     $('#monthly_allowance').on('keyup change', function() {
        //         let monthlyAllowance = parseFloat($(this).val());
        //         if (isNaN(monthlyAllowance) || monthlyAllowance <= 0) {
        //             // clear related fields if input invalid
        //             $('#hourly_allowance').val('');
        //             $('#worked_hours').val('');
        //             $('#bonus_name').val(''); // Assuming 'amount' input has id=bonus_name
        //             return;
        //         }
        
        //         let employeeId = $('#employee_select').val();
        //         let issueDate = $('#allowanceDate').val();
        
        //         if (!employeeId || !issueDate) {
        //             // Missing required params
        //             return;
        //         }
        
        //         let url = `https://qc.smitsmakkah.com/admin/allowances/get-employee-working-days/${employeeId}/${issueDate}`;
        
        //         $.ajax({
        //             url: url,
        //             type: 'GET',
        //             success: function(response) { 
        //                 if (response.total_worked && response.ideal_worked) {
        //                     let totalWorked = parseFloat(response.total_worked);
        //                     let idealWorked = parseFloat(response.ideal_worked);
        
        //                     // Calculate hour_allowance = monthly_allowance / idealWorked
        //                     let hourAllowance = monthlyAllowance / idealWorked;
        
        //                     // Calculate amount = hour_allowance * total_worked
        //                     let amount = hourAllowance * totalWorked;
        
        //                     $('#hourly_allowance').val(hourAllowance.toFixed(2));
        //                     $('#bonus_name').val(amount.toFixed(2));
        
        //                     // Show worked hours somewhere - you need to have an element for this
        //                     $('#worked_hours').val(totalWorked);
        //                 }
        //             },
        //             error: function(xhr) {
        //                 console.error('Error fetching working days:', xhr);
        //             }
        //         });
        //     });
        
        // });

    </script>
@endsection
