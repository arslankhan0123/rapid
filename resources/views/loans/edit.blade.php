@extends('layouts.app')
@section('title')
    {{ __('messages.loans.edit_loan') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.loans.edit_loan') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('loans.index') }}" class="btn btn-primary form-btn"> {{ __('messages.loans.list') }}</a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    {{ Form::open(['id' => 'editForm']) }}
                    {{ Form::hidden('id', $loan->id, ['id' => 'loan_id']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            {{ Form::label('approved_date', __('messages.common.date') . ':') }}<span
                                class="required">*</span>
                            {{ Form::date('date', $loan->date, ['class' => 'form-control', 'required']) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('employee_id', __('messages.overtimes.select_iqama') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select(
                                'employee_id',
                                $employees->mapWithKeys(function ($employee) {
                                    return [$employee->id => $employee->iqama_no . ' (' . $employee->name . ')'];
                                }),
                                $loan->employee_id ?? null,
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
                                    {{-- <p id="employeeSubDepartment" class="text-black"></p>
                                            <p id="employeeDepartment" class="text-black"></p> --}}
                                </div>
                                <div class="col-md-3 col-sm-12 image_preview">
                                    <img id="employeeImage" src="" alt="Employee Image"
                                        style="display: none; max-width: 200px; height: auto; border-radius: 5px;" />
                                </div>

                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('bonus_type_id', __('messages.branches.name') . ':') }}<span
                                class="required"></span>
                            {{ Form::select('from', $usersBranches, $loan->employee?->branch_id ?? null, [
                                'class' => 'form-control',
                                'id' => 'from_branch',
                                'style' => ' pointer-events: none; background-color: #e9ecef;',
                                'placeholder' => __('messages.branches.name'),
                            ]) }}
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('permitted_by', __('messages.loans.permitted_by') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::select('permitted_by', $employees->pluck('name', 'id'), $loan->permitted_by, ['class' => 'form-control', 'id' => 'permittd_by', 'required', 'placeholder' => __('messages.loans.select_permitted_by')]) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('amount', __('messages.loans.amount') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::number('amount', $loan->amount, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('approved_date', __('messages.loans.approved_date') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::date('approved_date', $loan->approved_date, ['class' => 'form-control', 'required']) }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('repayment_from', __('messages.loans.repayment_from') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::date('repayment_from', $loan->repayment_from, ['class' => 'form-control', 'required']) }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('interest_percentage', __('messages.loans.interest_percentage') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::number('interest_percentage', $loan->interest_percentage, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('installment_period', __('messages.loans.installment_period') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::number('installment_period', $loan->installment_period, ['class' => 'form-control', 'required']) }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('repayment_amount', __('messages.loans.repayment_amount') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::number('repayment_amount', $loan->repayment_amount, ['class' => 'form-control', 'step' => '0.01']) }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('installment', __('messages.loans.installment') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::number('installment', $loan->installment, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('status', __('messages.loans.status') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::select('status', ['active' => 'Active', 'inactive' => 'Inactive'], $loan->status, ['class' => 'form-control', 'required']) }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('description', __('messages.loans.description') . ':') }}
                            {{ Form::textarea('description', $loan->description, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
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
            let id = $('#loan_id').val();
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
                displayErrorMessage('{{ __('messages.department.select_department') }}');
                return false;
            }
            $.ajax({
                url: route('loans.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('loans.index', );
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
            function updateFields() {
                // Get values from the fields
                var amount = parseFloat($('#amount').val()) || 0;
                var interestPercentage = parseFloat($('#interest_percentage').val()) || 0;
                var repaymentAmount = parseFloat($('#repayment_amount').val()) || 0;
                var installmentPeriod = parseFloat($('#installment_period').val()) || 0;

                // Calculate the repayment amount if it's not set
                if ($('#repayment_amount').val() === '') {
                    repaymentAmount = amount + (amount * (interestPercentage / 100));
                    $('#repayment_amount').val(repaymentAmount.toFixed(2));
                }

                // Calculate the installment amount
                var installment = installmentPeriod > 0 ? (repaymentAmount / installmentPeriod).toFixed(2) : 0;

                // Update the installment field
                $('#installment').val(installment);
            }

            // Attach the function to the keyup event of the fields
            $('#amount, #interest_percentage').on('keyup', function() {
                var amount = parseFloat($('#amount').val()) || 0;
                var interestPercentage = parseFloat($('#interest_percentage').val()) || 0;

                // Calculate repayment amount and update the field
                var repaymentAmount = amount + (amount * (interestPercentage / 100));
                $('#repayment_amount').val(repaymentAmount.toFixed(2));

                // Update installment based on the new repayment amount and period
                updateFields();
            });

            $('#installment_period').on('keyup', function() {
                updateFields();
            });

            $('#employee_select').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#permittd_by').select2({
                width: '100%', // Set the width of the select element
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

                }
            }
        });
    </script>
@endsection
