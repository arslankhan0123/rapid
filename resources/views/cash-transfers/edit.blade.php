@extends('layouts.app')
@section('title')
    {{ __('messages.cash-transfers.edit') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.cash-transfers.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('cash-transfers.index') }}" class="btn btn-primary form-btn">List</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    {{ Form::open(['id' => 'editForm']) }}
                    {{ Form::hidden('id', $transfer->id, ['id' => 'leave_id']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group  col-md-12">
                            {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
                            {{ Form::select('branch_id', $usersBranches ?? [], $transfer->branch_id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'branchSelect', 'placeholder' => 'Select Branch']) }}
                        </div>
                        <!-- Transfer ID Field -->
                        <div class="form-group col-sm-12">
                            {{ Form::label('transfer_id', __('messages.cash-transfers.transfer_id')) }}<span
                                class="required">*</span>
                            {{ Form::text('transfer_id', $transfer->transfer_id, ['class' => 'form-control', 'required', 'id' => 'transfer_id', 'autocomplete' => 'off', 'readonly']) }}
                        </div>

                        <!-- From Account Field -->
                        <div class="form-group col-sm-12">
                            {{ Form::label('from_account', __('messages.cash-transfers.from_account')) }}<span
                                class="required">*</span>
                            {{ Form::select('from_account',$accounts->pluck('account_name', 'id') ?? [], $transfer->from_account, ['class' => 'form-control', 'required', 'id' => 'from_account']) }}
                        </div>

                        <!-- To Account Field -->
                        <div class="form-group col-sm-12">
                            {{ Form::label('to_account', __('messages.cash-transfers.to_account')) }}<span
                                class="required">*</span>
                            {{ Form::select('to_account', $accounts->pluck('account_name', 'id') ?? [], $transfer->to_account, ['class' => 'form-control', 'required', 'id' => 'to_account']) }}
                        </div>

                        <!-- Transfer Amount Field -->
                        <div class="form-group col-sm-12 mb-0">
                            {{ Form::label('transfer_amount', __('messages.cash-transfers.transfer_amount')) }}<span
                                class="required">*</span>
                            {{ Form::number('transfer_amount', $transfer->transfer_amount, ['class' => 'form-control', 'required', 'step' => '0.01', 'id' => 'transfer_amount', 'autocomplete' => 'off', 'step' => 'any']) }}
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



            $.ajax({
                url: route('cash-transfers.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('cash-transfers.index', );
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
