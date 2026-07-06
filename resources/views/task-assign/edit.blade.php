@extends('layouts.app')
@section('title')
    {{ __('messages.task-assign.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.task-assign.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('task-assign.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.task-assign.list') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    {{ Form::open(['id' => 'editForm']) }}
                    {{ Form::hidden('id', $task->id, ['id' => 'designation_id']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-12 col-md-6">
                            {{ Form::label('department', __('messages.common.department') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('department_id', $departments, $task->department_id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'department_select', 'placeholder' => 'All']) }}
                        </div>

                        <div class="form-group col-12 col-md-6">
                            {{ Form::label('designation', __('messages.designations.name') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('designation_id', [], $task->designation_id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'designation_select']) }}
                        </div>

                        <div class="form-group col-sm-12">
                            {{ Form::label('title', __('messages.task-status.task') . ':') }}<span
                                class="required">*</span>
                            {{ Form::textarea('name', $task->name, ['class' => 'form-control', 'style' => 'height:50px;', 'required', 'id' => 'designation_name', 'autocomplete' => 'off']) }}
                        </div>

                        {{-- <div class="form-group col-12 col-md-4">
                            {{ Form::label('employee_id', __('messages.employees.name') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('employee_id', [], $task->employee_id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'employee_select']) }}
                        </div> --}}
                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('description', __('messages.assets.category_description') . ':') }}
                            {{ Form::textarea('description', $task->description, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
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
            let id = $('#designation_id').val();

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
                url: route('task-assign.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('task-assign.index', );
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
            var allDesignations = @json($designations); // Get all designations data
            var allEmployees = @json($employees); // Get all employees data
            // Get the default values for designation_id and employee_id
            var defaultDesignationId = @json($task->designation_id ?? ''); // Default designation ID from server
            var defaultEmployeeId = @json($task->employee_id ?? ''); // Default employee ID from server



            // Function to populate designations dropdown
            function populateDesignations(departmentId) {
                var designationSelect = $('#designation_select');
                designationSelect.empty(); // Clear current options

                // Add 'All' option
                designationSelect.append(new Option('All', ''));

                // Filter designations by department
                $.each(allDesignations, function(index, designation) {
                    if (departmentId === '' || designation.department_id == departmentId) {
                        var option = new Option(designation.name, designation.id);

                        // Set the default value for designation_id
                        if (designation.id == defaultDesignationId) {
                            option.selected = true;
                        }

                        designationSelect.append(option);
                    }
                });

                designationSelect.trigger('change'); // Trigger change event to update employees
            }

            // Function to populate employees dropdown
            function populateEmployees(departmentId, designationId) {
                var employeeSelect = $('#employee_select');
                employeeSelect.empty(); // Clear current options

                // Add 'All' option
                employeeSelect.append(new Option('All', ''));

                // Filter employees by department and designation
                $.each(allEmployees, function(index, employee) {
                    if (
                        (departmentId === '' || employee.department_id == departmentId) &&
                        (designationId === '' || employee.designation_id == designationId)
                    ) {
                        var option = new Option(employee.name, employee.id);

                        // Set the default value for employee_id
                        if (employee.id == defaultEmployeeId) {
                            option.selected = true;
                        }

                        employeeSelect.append(option);
                    }
                });

                employeeSelect.trigger('change');
            }

            // Initialize designations and employees with the default values
            populateDesignations(''); // Pass empty string for all departments
            populateEmployees('', defaultDesignationId); // Pre-select employees based on default designation

            // Handle department selection change
            $('#department_select').on('change', function() {
                var departmentId = $(this).val(); // Get selected department ID
                populateDesignations(departmentId); // Update designations based on department
                populateEmployees(departmentId,
                    ''); // Update employees with the selected department and all designations
            });

            // Handle designation selection change
            $('#designation_select').on('change', function() {
                var departmentId = $('#department_select').val(); // Get selected department ID
                var designationId = $(this).val(); // Get selected designation ID
                populateEmployees(departmentId,
                    designationId); // Update employees based on department and designation
            });
        });
    </script>
@endsection
