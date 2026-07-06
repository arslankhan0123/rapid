@extends('layouts.app')
@section('title')
    {{ __('messages.accounts.add') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.accounts.add') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('accounts.index') }}" class="btn btn-primary form-btn">List</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    {{ Form::open(['id' => 'addNewFormDepartmentNew']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group  col-md-12">
                            {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
                            {{ Form::select('branch_id', $usersBranches ?? [], null, ['class' => 'form-control select2', 'required', 'id' => 'branchSelect', 'placeholder' => 'Select Branch']) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('account_number', __('messages.accounts.account_number')) }}<span
                                class="required">*</span>
                            {{ Form::text('account_number', '', ['class' => 'form-control', 'required', 'id' => 'account_number', 'autocomplete' => 'off', 'readonly']) }}
                        </div>
                        <!-- Account Name Field -->
                        <div class="form-group col-sm-12">
                            {{ Form::label('account_name', __('messages.accounts.account_name')) }}<span
                                class="required">*</span>
                            {{ Form::text('account_name', null, ['class' => 'form-control', 'required', 'id' => 'account_name', 'autocomplete' => 'off']) }}
                        </div>

                        <!-- Account Number Field -->


                        <!-- Opening Balance Field -->
                        <div class="form-group col-sm-12 ">
                            {{ Form::label('opening_balance', __('messages.accounts.opening_balance')) }}<span
                                class="required">*</span>
                            {{ Form::number('opening_balance', 0, ['class' => 'form-control', 'required', 'step' => '0.01', 'id' => 'opening_balance', 'autocomplete' => 'off']) }}
                        </div>
                        <div class="form-group col-sm-12 mb-4">
                            {{ Form::label('employee_id', __('messages.accounts.received_by') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('received_by', $employees, null, ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.awards.select_employee')]) }}
                        </div>
                    </div>


                    <div class="text-right mr-1 mt-2">
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
        let departmentNewCreateUrl = route('accounts.store');
        $(document).on('submit', '#addNewFormDepartmentNew', function(event) {
            event.preventDefault();
            processingBtn('#addNewFormDepartmentNew', '#btnSave', 'loading');


            $.ajax({
                url: departmentNewCreateUrl,
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        $('#bonus_name').val('');
                        const url = route('accounts.index', );
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
        $(document).ready(function() {
            const accountNumbers = @json($accountNumbers); // Parse account numbers from server-side
            console.log(accountNumbers);

            $('#branchSelect').change(function() {
                const branchId = $(this).val(); // Get the selected branch ID
                if (branchId) {
                    // Check if the branch exists in accountNumbers
                    if (accountNumbers[branchId]) {
                        // Extract and increment the last number for the branch
                        const lastAccount = accountNumbers[branchId];
                        const lastNumber = parseInt(lastAccount.split('-')[1]); // Get the numeric part
                        const incrementedNumber = lastNumber + 1; // Increment the number
                        $('#account_number').val(branchId + '-' +
                        incrementedNumber); // Set the new account number
                    } else {
                        // If the branch ID is not found in accountNumbers, start from 1000
                        $('#account_number').val(branchId + '-1000');
                    }
                } else {
                    // Clear the account number field if no branch is selected
                    $('#account_number').val('');
                }
            });
        });
    </script>
@endsection
