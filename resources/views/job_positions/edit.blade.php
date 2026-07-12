@extends('layouts.app')
@section('title')
    Edit Job Position
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>Edit Job Position</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('job-positions.index') }}" class="btn btn-primary form-btn">Job Positions List</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="modal-content">
                        {{ Form::model($jobPosition, ['route' => ['job-positions.update', $jobPosition->id], 'method' => 'put', 'id' => 'editJobPositionForm']) }}
                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    {{ Form::label('position_code', 'Position Code:') }}
                                    {{ Form::text('position_code', null, ['class' => 'form-control', 'id' => 'position_code', 'autocomplete' => 'off', 'placeholder' => 'Leave blank for auto-generation']) }}
                                </div>
                                <div class="form-group col-sm-6">
                                    {{ Form::label('title', 'Position Title:') }}<span class="required">*</span>
                                    {{ Form::text('title', null, ['class' => 'form-control', 'required', 'id' => 'title', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-6">
                                    {{ Form::label('category_id', 'Job Category:') }}
                                    {{ Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'id' => 'category_id', 'placeholder' => 'Select Category']) }}
                                </div>
                                <div class="form-group col-sm-6">
                                    {{ Form::label('department_id', 'Department:') }}
                                    {{ Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'id' => 'department_id', 'placeholder' => 'Select Department']) }}
                                </div>
                                <div class="form-group col-sm-6">
                                    {{ Form::label('employment_type', 'Employment Type:') }}
                                    {{ Form::select('employment_type', $employmentTypes, null, ['class' => 'form-control select2', 'id' => 'employment_type', 'placeholder' => 'Select Employment Type']) }}
                                </div>
                                <div class="form-group col-sm-6">
                                    {{ Form::label('experience_required', 'Experience Required:') }}
                                    {{ Form::text('experience_required', null, ['class' => 'form-control', 'id' => 'experience_required', 'placeholder' => 'e.g. 2 Years']) }}
                                </div>
                                <div class="form-group col-sm-6">
                                    {{ Form::label('min_salary', 'Minimum Salary:') }}
                                    {{ Form::number('min_salary', null, ['class' => 'form-control', 'id' => 'min_salary', 'step' => '0.01', 'min' => 0]) }}
                                </div>
                                <div class="form-group col-sm-6">
                                    {{ Form::label('max_salary', 'Maximum Salary:') }}
                                    {{ Form::number('max_salary', null, ['class' => 'form-control', 'id' => 'max_salary', 'step' => '0.01', 'min' => 0]) }}
                                </div>
                                <div class="form-group col-sm-6">
                                    {{ Form::label('status', 'Status:') }}
                                    {{ Form::select('status', $statuses, null, ['class' => 'form-control select2', 'id' => 'status', 'placeholder' => 'Select Status']) }}
                                </div>
                                <div class="form-group col-sm-6">
                                    {{ Form::label('skills', 'Required Skills:') }}
                                    {{ Form::select('skills[]', $skills, $selectedSkills, ['class' => 'form-control select2', 'id' => 'skills', 'multiple' => 'multiple', 'data-placeholder' => 'Select Required Skills']) }}
                                </div>
                                <div class="form-group col-sm-12 mb-0">
                                    {{ Form::label('description', 'Job Description:') }}
                                    {{ Form::textarea('description', null, ['class' => 'form-control summernote-simple', 'id' => 'editDescription']) }}
                                </div>
                            </div>
                            <div class="text-right mr-1 mt-3">
                                {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnEditSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
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
        $(document).ready(function() {
            $('#category_id').select2();
            $('#department_id').select2();
            $('#employment_type').select2();
            $('#status').select2();
            $('#skills').select2();
        });

        $(document).on('submit', '#editJobPositionForm', function(event) {
            event.preventDefault();
            processingBtn('#editJobPositionForm', '#btnEditSave', 'loading');

            let description = $('<div />').html($('#editDescription').summernote('code'));
            let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

            if ($('#editDescription').summernote('isEmpty')) {
                $('#editDescription').val('');
            } else if (empty) {
                displayErrorMessage('Description field does not contain only white space');
                processingBtn('#editJobPositionForm', '#btnEditSave', 'reset');
                return false;
            }

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('job-positions.index');
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#editJobPositionForm', '#btnEditSave');
                },
            });
        });
    </script>
@endsection
