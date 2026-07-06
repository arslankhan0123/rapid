@extends('layouts.app')
@section('title')
    {{ __('messages.terms.add') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <style>
        #statusSwitch.form-check-input {
            width: 3em;
            height: 1.5em;
        }

        #statusSwitch.form-check-input:checked {
            background-color: #0d6efd;
            /* Change the color as needed */
        }

        #statusSwitch.form-check-input::before {
            width: 1.5em;
            height: 1.5em;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.terms.add') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('terms.index') }}" class="btn btn-primary form-btn">{{ __('messages.terms.list') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    {{ Form::open(['id' => 'addNewFormDepartmentNew']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('description', __('messages.terms.name') . ':') }}
                            <span class="text-danger">*</span>
                            {{ Form::textarea('terms', null, ['class' => 'form-control summernote-simple', 'id' => 'createDescription', 'required']) }}
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
        let departmentNewCreateUrl = route('terms.store');
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
                        const url = route('terms.index', );
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
    </script>
@endsection
