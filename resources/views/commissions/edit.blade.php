@extends('layouts.app')
@section('title')
    {{ __('messages.commissions.edit') }}
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
            <h1>{{ __('messages.commissions.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('commissions.index') }}" class="btn btn-primary form-btn">{{ __('messages.commissions.list') }}</a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="modal-content">
                        {{ Form::open(['id' => 'editForm']) }}
                        {{ Form::hidden('id', $commission->id, ['id' => 'commission_id']) }}
                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                            <div class="row">

                                <div class="form-group col-sm-12 ">
                                    {{ Form::label('description', __('messages.commissions.employee_number') . ':') }}
                                    {{ Form::text('employee_number', $employeeData->id, ['class' => 'form-control', 'id' => 'employee_number', 'disabled']) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('employee_id', __('messages.commissions.select_employee') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('employee_id', $employees, $commission->employee_id ?? null, ['class' => 'form-control', 'required', 'id' => 'employee_select', 'placeholder' => __('messages.commissions.select_employee')]) }}
                                </div>

                                <div class="form-group col-sm-12 ">
                                    {{ Form::label('description', __('messages.commissions.department') . ':') }}
                                    {{ Form::text('department', $employeeData->department->name, ['class' => 'form-control', 'id' => 'department', 'disabled']) }}
                                </div>
                                <div class="form-group col-sm-12 ">
                                    {{ Form::label('description', __('messages.commissions.sub_department') . ':') }}
                                    {{ Form::text('sub_department', $employeeData->subDepartment->name, ['class' => 'form-control', 'id' => 'sub_department', 'disabled']) }}
                                </div>
                                <div class="form-group col-sm-12 ">
                                    {{ Form::label('description', __('messages.commissions.designation') . ':') }}
                                    {{ Form::text('designation', $employeeData->designation->name, ['class' => 'form-control', 'id' => 'designation', 'disabled']) }}
                                </div>


                                <div class="form-group col-sm-12 ">
                                    {{ Form::label('commission', __('messages.commissions.name') . ':') }}
                                    {{ Form::number('commission', $commission->commission, ['class' => 'form-control','required', 'id' => 'commision']) }}
                                </div>

                                <div class="form-group col-sm-12 mb-0">
                                    {{ Form::label('description', __('messages.commissions.description') . ':') }}
                                    {{ Form::textarea('description', $commission->description, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
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
                displayErrorMessage('{{ __('messages.commissions.select_department') }}');
                return false;
            }
            $.ajax({
                url: route('commissions.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('commissions.index', );
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
                placeholder: '{{ __('messages.commissions.select_employee') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });


            $('#employee_select').on('change', function() {
                var employeeId = $(this).val();
                // Check if an employee ID is selected
                if (employeeId) {
                    $.ajax({
                        url: route('commissions.employee.info',
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
@endsection
