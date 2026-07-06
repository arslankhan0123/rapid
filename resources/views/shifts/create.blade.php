@extends('layouts.app')
@section('title')
    {{ __('messages.shifts.add') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jscolor/2.4.5/jscolor.min.css">

@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.shifts.add') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('shifts.index') }}" class="btn btn-primary form-btn">List</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="modal-content">
                        {{ Form::open(['id' => 'addNewFormDepartmentNew']) }}
                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        {{ Form::label('name', __('messages.shifts.name') . ':') }}<span
                                            class="required">*</span>
                                        {{ Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'holiday_name', 'autocomplete' => 'off']) }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        {{ Form::label('name', __('messages.shifts.color') . ':') }}<span
                                            class="required">*</span>
                                        {{ Form::text('color', null, ['class' => 'form-control jscolor', 'required', 'id' => 'color', 'autocomplete' => 'off']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group ">
                                        {{ Form::label('from_date', __('messages.shifts.shift_start_time') . ':') }}<span
                                            class="required">*</span>
                                        {{ Form::time('shift_start_time', null, ['class' => 'form-control', 'required']) }}
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group ">
                                        {{ Form::label('end_date', __('messages.shifts.shift_end_time') . ':') }}<span
                                            class="required">*</span>
                                        {{ Form::time('shift_end_time', null, ['class' => 'form-control', 'required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group ">
                                        {{ Form::label('lunch_start_time', __('messages.shifts.lunch_start_time') . ':') }}
                                        {{ Form::time('lunch_start_time', null, ['class' => 'form-control', 'required']) }}
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group ">
                                        {{ Form::label('lunch_end_time', __('messages.shifts.lunch_end_time') . ':') }}
                                        {{ Form::time('lunch_end_time', null, ['class' => 'form-control', 'required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {{ Form::label('title', __('messages.shifts.description') . ':') }}
                                    {{ Form::textarea('description', null, ['class' => 'form-control summernote-simple', 'id' => 'createDescription', 'placeholder' => __('messages.assets.asset_note')]) }}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscolor/2.4.5/jscolor.min.js"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        let departmentNewCreateUrl = route('shifts.store');
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
                        const url = route('shifts.index', );
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
    <script>
        jscolor.presets.default = {
            width: 200,
            height: 200,
            borderWidth: 1,
            borderColor: '#000000',
            insetColor: '#ffffff',
            backgroundColor: '#ffffff',
            previewPosition:'right',
        };
    </script>
@endsection
