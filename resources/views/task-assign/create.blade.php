@extends('layouts.app')
@section('title')
    {{ __('messages.task-assign.add') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.task-assign.add') }}</h1>
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

                    {{ Form::open(['id' => 'addNewFormDepartmentNew']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">

                        <div class="form-group col-12 col-md-6">
                            {{ Form::label('department', __('messages.common.department') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'required', 'id' => 'department_select', 'placeholder' => 'All']) }}
                        </div>

                        <div class="form-group col-12 col-md-6">
                            {{ Form::label('designation', __('messages.designations.name') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('designation_id', [], null, ['class' => 'form-control select2', 'required', 'id' => 'designation_select']) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('title', __('messages.task-status.task') . ':') }}<span
                                class="required">*</span>
                            {{ Form::textarea('name', null, ['class' => 'form-control ', 'required', 'style' => 'height:50px;', 'autocomplete' => 'off']) }}
                        </div>

                        {{-- <div class="form-group col-12 col-md-4">
                            {{ Form::label('employee_id', __('messages.employees.name') . ':') }}<span class="required">*</span>
                            {{ Form::select('employee_id', [], null, ['class' => 'form-control select2', 'required', 'id' => 'employee_select']) }}
                        </div> --}}
                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('description', __('messages.task-assign.description') . ':') }}
                            {{ Form::textarea('description', null, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
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
        let departmentNewCreateUrl = route('task-assign.store');
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
                        const url = route('task-assign.index', );
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


        $(document).on('click', '.edit-btn', function(event) {
            let id = $(event.currentTarget).data('id');
            const url = route('task-assign.edit', id);
            window.location.href = url;
        });

        $(document).on('click', '.delete-btn', function(event) {
            let departmentId = $(event.currentTarget).data('id');
            deleteItem(route('task-assign.destroy', departmentId), '#designationTable',
                '{{ __('messages.task-assign.name') }}');
        });
    </script>

    <script>
        $(document).ready(function() {
            var allDesignations = @json($designations); // Get all designations data
            var allEmployees = @json($employees); // Get all employees data

            // Function to populate designations dropdown
            function populateDesignations(departmentId) {
                var designationSelect = $('#designation_select');
                designationSelect.empty(); // Clear current options

                // Add 'All' option
                designationSelect.append(new Option('All', ''));

                // Filter designations by department
                $.each(allDesignations, function(index, designation) {
                    if (departmentId === '' || designation.department_id == departmentId) {
                        designationSelect.append(new Option(designation.name, designation.id));
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
                        employeeSelect.append(new Option(employee.name, employee.id));
                    }
                });

                employeeSelect.trigger('change');
            }

            // Initialize designations and employees with 'All' options
            populateDesignations('');
            populateEmployees('', '');

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
