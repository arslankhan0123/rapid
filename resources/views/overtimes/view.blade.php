@extends('layouts.app')
@section('title')
    {{ __('messages.overtimes.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.overtimes.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('overtimes.index') }}" class="btn btn-primary form-btn">List</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.employees.name')) }}</strong>
                            <p style="color: #555;">{{ $overtime->employee->name }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.overtimes.type')) }}</strong>
                            <p style="color: #555;">{{ $overtime->overtimeTypes->name }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.overtimes.amount')) }}</strong>
                            <p style="color: #555;">{{ $overtime->amount }}</p>
                        </div>
                        <div class="form-group col-sm-6 mb-0">
                            <strong>{{ Form::label('description', __('messages.common.created_on')) }}</strong>
                            <div style="color: #555;">{{ $overtime->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="form-group col-sm-12 mb-0">
                            <strong>{{ Form::label('description', __('messages.overtimes.description')) }}</strong>
                            {!! $overtime->description !!}
                        </div>
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
            let id = $('#bonus_id').val();

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
                url: route('overtimes.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('overtimes.index', );
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
@endsection
