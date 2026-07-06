@extends('layouts.app')
@section('title')
    {{ __('messages.designations.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.designations.edit_designation') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('designations.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.designations.list') }}</a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                        {{ Form::open(['id' => 'editForm']) }}
                        {{ Form::hidden('id', $designation->id, ['id' => 'designation_id']) }}
                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    {{ Form::label('title', __('messages.designations.name') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('name', $designation->name, ['class' => 'form-control', 'required', 'id' => 'designation_name', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('department_id', __('messages.department.select_department') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('department_id', $departments, $designation->department_id, ['class' => 'form-control', 'id' => 'departmentSelect', 'placeholder' => __('messages.department.select_department')]) }}
                                </div>
                                {{-- <div class="form-group col-sm-12">
                                    {{ Form::label('sub_department_id', __('messages.department.select_sub_department') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('sub_department_id', $subDepartments->pluck('name', 'id'), $designation->sub_department_id, ['class' => 'form-control', 'required', 'id' => 'subDepartmentSelect', 'placeholder' => __('messages.department.select_sub_department')]) }}
                                </div> --}}
                                <div class="form-group col-sm-12 mb-0">
                                    {{ Form::label('description', __('messages.assets.category_description') . ':') }}
                                    {{ Form::textarea('description', $designation->description, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
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
                url: route('designations.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);

                        const url = route('designations.index', );
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
            const subDepartments = @json($subDepartments);

            function updateSubDepartments() {
                const departmentId = $('#departmentSelect').val();
                const filteredSubDepartments = subDepartments.filter(sub => sub.department_id == departmentId);

                $('#subDepartmentSelect').empty();
                $('#subDepartmentSelect').append(
                    '<option value="">{{ __('messages.department.select_sub_department') }}</option>');
                $.each(filteredSubDepartments, function(index, sub) {
                    $('#subDepartmentSelect').append(
                        $('<option>', {
                            value: sub.id,
                            text: sub.name
                        })
                    );
                });
                // Set the previously selected sub-department if available
                const selectedSubDepartment = '{{ old('sub_department_id', $designation->sub_department_id) }}';
                $('#subDepartmentSelect').val(selectedSubDepartment);
            }
            $('#departmentSelect').on('change', updateSubDepartments);
            // Initialize the sub-department dropdown on page load
            updateSubDepartments();
        });
    </script>
@endsection
