@extends('layouts.app')
@section('title')
    {{ __('messages.journal-vouchers.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.journal-vouchers.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('journal-vouchers.index') }}" class="btn btn-primary form-btn">List</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('title', __('messages.branches.name')) }}</strong>
                            <p style="color: #555;">{{ $voucher->branch?->name ?? '' }}</p>
                        </div>
                         <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('title', "From Account") }}</strong>
                            <p style="color: #555;">{{ $voucher->fromAccount?->account_name ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('title', "To Account") }}</strong>
                            <p style="color: #555;">{{ $voucher->account?->account_name ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('title', __('messages.journal-vouchers.amount')) }}</strong>
                            <p style="color: #555;">{{ $voucher->amount??'' }}</p>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                {{ Form::label('note', __('messages.journal-vouchers.notes')) }}
                                <br>{!! isset($voucher->description) ? html_entity_decode($voucher->description) : __('messages.common.n/a') !!}
                            </div>
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
                url: route('journal-vouchers.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('journal-vouchers.index', );
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
