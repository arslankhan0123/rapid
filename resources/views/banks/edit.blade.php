@extends('layouts.app')
@section('title')
    {{ __('messages.banks.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.banks.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('banks.index') }}" class="btn btn-primary form-btn">List</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    {{ Form::open(['id' => 'editForm']) }}
                    {{ Form::hidden('id', $bank->id, ['id' => 'leave_id']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            {{ Form::label('title', __('messages.banks.name')) }}<span class="required">*</span>
                            {{ Form::text('name', $bank->name, ['class' => 'form-control', 'required', 'id' => 'designation_name', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('title', __('messages.banks.account_number')) }}<span class="required">*</span>
                            {{ Form::text('account_number', $bank->account_number, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('title', __('messages.banks.branch_name')) }}
                            {{ Form::text('branch_name', $bank->branch_name, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('title', __('messages.banks.iban_number')) }}<span class="required">*</span>
                            {{ Form::text('iban_number', $bank->iban_number ?? '', ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('title', __('messages.banks.opening_balance')) }}<span class="required">*</span>
                            {{ Form::number('opening_balance', $bank->opening_balance ?? 0, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'step' => 'any']) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('title', __('messages.banks.address')) }}
                            {{ Form::textarea('address', $bank->address ?? '', ['class' => 'form-control', 'autocomplete' => 'off']) }}
                        </div>

                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('description', __('messages.banks.description')) }}
                            {{ Form::textarea('description', $bank->description, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
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
            let id = $('#leave_id').val();

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
                url: route('banks.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('banks.index', );
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
