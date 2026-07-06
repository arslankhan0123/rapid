@extends('layouts.app')
@section('title')
    {{ __('messages.master_accounts.add_master_account') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <style>
        .dynamic-field {
            display: none;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.master_accounts.add_master_account') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('master-accounts.index') }}" class="btn btn-primary form-btn float-right">Back</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => 'master-accounts.store', 'id' => 'createMasterAccountForm']) }}
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            {{ Form::label('account_number', __('messages.master_accounts.account_number')) }}
                            <span class="required">*</span>
                            {{ Form::text('account_number', null, [
                                'class' => 'form-control',
                                'required',
                            ]) }}
                        </div>

                        <div class="form-group col-sm-6">
                            {{ Form::label('name', __('messages.master_accounts.name')) }}
                            <span class="required">*</span>
                            {{ Form::text('name', null, [
                                'class' => 'form-control',
                                'required',
                            ]) }}
                        </div>

                        <div class="form-group col-sm-6">
                            {{ Form::label('account_level', __('messages.master_accounts.account_level')) }}
                            <span class="required">*</span>
                            {{ Form::select(
                                'account_level',
                                [
                                    'level-1' => 'Level 1',
                                    'level-2' => 'Level 2',
                                    'level-3' => 'Level 3',
                                    'level-4' => 'Level 4',
                                ],
                                null,
                                [
                                    'class' => 'form-control',
                                    'required',
                                    'id' => 'account_level',
                                    'placeholder' => __('messages.master_accounts.select_account_level'),
                                ],
                            ) }}
                        </div>

                        <div class="form-group col-sm-6">
                            {{ Form::label('account_type', __('messages.master_accounts.account_type')) }}
                            {{ Form::select(
                                'account_type',
                                [
                                    'Assets' => 'Assets',
                                    'Liabilities' => 'Liabilities',
                                    'Equity' => 'Equity',
                                ],
                                null,
                                [
                                    'class' => 'form-control',
                                    'id' => 'account_type',
                                    'placeholder' => __('messages.master_accounts.select_account_type'),
                                ],
                            ) }}
                        </div>

                        <!-- New Fields for Report Type and Amount Type -->
                        <div class="form-group col-sm-6">
                            {{ Form::label('report_type', __('messages.master_accounts.report_type')) }}
                            <span class="required">*</span>
                            {{ Form::select(
                                'report_type',
                                [
                                    '' => 'Select Report Type',
                                    'Trading Account' => 'Trading Account',
                                    'Profit and Loss Account' => 'Profit and Loss Account',
                                    'Balance Sheet' => 'Balance Sheet',
                                ],
                                null,
                                [
                                    'class' => 'form-control',
                                    'id' => 'report_type',
                                ],
                            ) }}
                        </div>

                        <div class="form-group col-sm-6">
                            {{ Form::label('amount_type', __('messages.master_accounts.amount_type')) }}
                            <span class="required">*</span>
                            {{ Form::select(
                                'amount_type',
                                [
                                    '' => 'Select Amount Type',
                                    'Debit' => 'Debit',
                                    'Credit' => 'Credit',
                                ],
                                null,
                                [
                                    'class' => 'form-control',
                                    'id' => 'amount_type',
                                ],
                            ) }}
                        </div>

                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('description', __('messages.master_accounts.description')) }}
                            {{ Form::textarea('description', null, [
                                'class' => 'form-control summernote-simple',
                                'id' => 'description',
                                'rows' => 4,
                            ]) }}
                        </div>
                    </div>

                    {{-- <div class="form-group col-sm-12 mb-0 mt-3">
                        <div class="form-check form-switch ">
                            {{ Form::label('status', __('messages.master_accounts.status')) }}
                            {{ Form::checkbox('status', 1, true, ['class' => 'form-check-input ml-4', 'id' => 'statusSwitch']) }}
                        </div>
                    </div> --}}
                    <div class="text-right mt-3">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#account_level, #account_type, #report_type, #amount_type').select2({
                width: '100%',
                placeholder: 'Select an option',
                allowClear: true
            });
        });

        $(document).on('submit', '#createMasterAccountForm', function(event) {
            event.preventDefault();
            processingBtn('#createMasterAccountForm', '#btnSave', 'loading');

            $.ajax({
                url: "{{ route('master-accounts.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        window.location.href = "{{ route('master-accounts.index') }}";
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#createMasterAccountForm', '#btnSave');
                },
            });
        });
    </script>
@endsection
