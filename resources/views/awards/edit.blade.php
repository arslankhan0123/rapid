@extends('layouts.app')
@section('title')
    {{ __('messages.awards.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <style>
        #statusSwitchEdit.form-check-input {
            width: 3em;
            height: 1.5em;
        }

        #statusSwitchEdit.form-check-input:checked {
            background-color: #0d6efd;
            /* Change the color as needed */
        }

        #statusSwitchEdit.form-check-input::before {
            width: 1.5em;
            height: 1.5em;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.awards.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('awards.index') }}" class="btn btn-primary form-btn">{{ __('messages.awards.list') }}</a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="modal-content">
                        {{ Form::open(['id' => 'editForm']) }}
                        {{ Form::hidden('id', $award->id, ['id' => 'award_id']) }}
                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    {{ Form::label('name', __('messages.awards.award_name') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('name', $award->name ?? null, ['class' => 'form-control', 'required', 'id' => 'award_name', 'autocomplete' => 'off']) }}
                                </div>

                                <div class="form-group col-sm-12">
                                    {{ Form::label('employee_id', __('messages.awards.select_employee') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('employee_id', $employees, $award->employee_id ?? null, ['class' => 'form-control', 'required', 'id' => 'employee_select', 'placeholder' => __('messages.awards.select_employee')]) }}
                                </div>

                                <div class="form-group col-sm-12">
                                    {{ Form::label('gift', __('messages.awards.gift') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('gift', $award->gift ?? null, ['class' => 'form-control', 'required', 'id' => 'award_gift', 'autocomplete' => 'off']) }}
                                </div>

                                <div class="form-group col-sm-12">
                                    {{ Form::label('date', __('messages.awards.date') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::date('date', $award->date ?? null, ['class' => 'form-control', 'required', 'id' => 'award_date']) }}
                                </div>

                                <div class="form-group col-sm-12">
                                    {{ Form::label('award_by', __('messages.awards.by') . ':') }}
                                    {{ Form::select('award_by', $employees, $award->award_by ?? null, ['class' => 'form-control', 'id' => 'award_by_select', 'placeholder' => __('messages.awards.by')]) }}
                                </div>

                                <div class="form-group col-sm-12 mb-0">
                                    {{ Form::label('description', __('messages.awards.description') . ':') }}
                                    {{ Form::textarea('description', $award->description ?? null, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
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
            let id = $('#award_id').val();
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
                displayErrorMessage('{{ __('messages.awards.select_department') }}');
                return false;
            }
            $.ajax({
                url: route('awards.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('awards.index', );
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
                placeholder: '{{ __('messages.awards.select_employee') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });
             $('#award_by_select').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.awards.by') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });
        });
    </script>
@endsection
