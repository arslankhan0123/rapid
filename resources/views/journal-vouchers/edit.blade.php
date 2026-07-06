@extends('layouts.app')
@section('title')
    {{ __('messages.journal-vouchers.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.journal-vouchers.edit') }}</h1>
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

                    {{ Form::open(['id' => 'editForm']) }}
                    {{ Form::hidden('id', $voucher->id, ['id' => 'leave_id']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <!-- Transfer ID Field -->
                        <div class="form-group  col-md-12">
                            {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
                            {{ Form::select('branch_id', $usersBranches ?? [], $voucher->branch_id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'branchSelect', 'placeholder' => 'Select Branch']) }}
                        </div>


                        <div class="form-group col-sm-12">
                            {{ Form::label('from_account', 'From Account') }}<span class="required">*</span>
                            {{ Form::select('from_account', $accounts->pluck('account_name', 'id') ?? [],  $voucher->from_account ?? null, ['class' => 'form-control', 'required', 'id' => 'from_account']) }}
                        </div>
                        <!-- From Account Field -->
                        <div class="form-group col-sm-12">
                            {{ Form::label('from_account', 'To Account') }}<span class="required">*</span>
                            {{ Form::select('account_id', $accounts->pluck('account_name', 'id') ?? [], $voucher->account_id ?? null, ['class' => 'form-control', 'required', 'id' => 'to_account']) }}
                        </div>

                        <!-- Transfer Amount Field -->
                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('transfer_amount', __('messages.journal-vouchers.amount')) }}<span
                                class="required">*</span>
                            {{ Form::number('amount', $voucher->amount ?? 0, ['class' => 'form-control', 'required', 'step' => '0.01', 'id' => 'transfer_amount', 'autocomplete' => 'off', 'step' => 'any']) }}
                        </div>

                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('description', __('messages.journal-vouchers.notes') . ':') }}
                            {{ Form::textarea('description', $voucher->description ?? '', ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
                        </div>
                    </div>
                    <div class="text-right mr-1 mt-2">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing...", 'style' => 'line-height:30px;']) }}

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

    <script>
        // Pass PHP data to JavaScript as a JSON object
        const accountsData = @json($accounts);
        $(document).ready(function() {
            const accountsByBranch = {};

            // Prepare accounts by branch
            accountsData.forEach(account => {
                if (!accountsByBranch[account.branch_id]) {
                    accountsByBranch[account.branch_id] = [];
                }
                accountsByBranch[account.branch_id].push({
                    id: account.id,
                    name: account.account_name,
                });
            });

            // Handle branch selection change
            $('#branchSelect').change(function() {
                const branchId = $(this).val();
                const filteredAccounts = accountsByBranch[branchId] || [];

                // Update options for from_account and to_account
                updateDropdown('#from_account', filteredAccounts);
                updateDropdown('#to_account', filteredAccounts);
            });

            function updateDropdown(selector, accounts) {
                const dropdown = $(selector);
                dropdown.empty();
                dropdown.append('<option value="" disabled selected>Select Account</option>');
                accounts.forEach(account => {
                    dropdown.append(`<option value="${account.id}">${account.name}</option>`);
                });
            }
        });
    </script>
@endsection
