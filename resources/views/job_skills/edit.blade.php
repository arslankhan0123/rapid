@extends('layouts.app')
@section('title')
    Edit Job Skill
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>Edit Job Skill</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('job-skills.index') }}" class="btn btn-primary form-btn">Job Skills List</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="modal-content">
                        {{ Form::model($jobSkill, ['route' => ['job-skills.update', $jobSkill->id], 'method' => 'put', 'id' => 'editJobSkillForm']) }}
                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    {{ Form::label('skill_code', 'Skill Code:') }}
                                    {{ Form::text('skill_code', null, ['class' => 'form-control', 'id' => 'skill_code', 'autocomplete' => 'off', 'placeholder' => 'Leave blank for auto-generation']) }}
                                </div>
                                <div class="form-group col-sm-6">
                                    {{ Form::label('name', 'Skill Name:') }}<span class="required">*</span>
                                    {{ Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'name', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-6">
                                    {{ Form::label('category_id', 'Skill Category:') }}
                                    {{ Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'id' => 'category_id', 'placeholder' => 'Select Category']) }}
                                </div>
                                <div class="form-group col-sm-6">
                                    {{ Form::label('level', 'Skill Level:') }}
                                    {{ Form::select('level', $levels, null, ['class' => 'form-control select2', 'id' => 'level', 'placeholder' => 'Select Level']) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="custom-switch mt-2 pl-0">
                                        <input type="checkbox" name="is_active" value="1" class="custom-switch-input" {{ $jobSkill->is_active ? 'checked' : '' }}>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Status (Active/Inactive)</span>
                                    </label>
                                </div>
                                <div class="form-group col-sm-12 mb-0">
                                    {{ Form::label('description', 'Description:') }}
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
            $('#level').select2();
        });

        $(document).on('submit', '#editJobSkillForm', function(event) {
            event.preventDefault();
            processingBtn('#editJobSkillForm', '#btnEditSave', 'loading');

            let description = $('<div />').html($('#editDescription').summernote('code'));
            let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

            if ($('#editDescription').summernote('isEmpty')) {
                $('#editDescription').val('');
            } else if (empty) {
                displayErrorMessage('Description field does not contain only white space');
                processingBtn('#editJobSkillForm', '#btnEditSave', 'reset');
                return false;
            }

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('job-skills.index');
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#editJobSkillForm', '#btnEditSave');
                },
            });
        });
    </script>
@endsection
