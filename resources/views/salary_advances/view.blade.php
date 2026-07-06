@extends('layouts.app')
@section('title')
    {{ __('messages.salary_advances.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.salary_advances.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('salary_advances.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.salary_advances.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('employee_id', __('messages.common.date')) }}</strong>
                                <p style="color: #555;">{{ \Carbon\Carbon::parse($salaryAdvance->date)->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('employee_id', __('messages.salary_advances.employee')) }}</strong>
                                <p style="color: #555;">{{ $salaryAdvance->employee->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('employee_id', __('messages.salary_advances.designation')) }}</strong>
                                <p style="color: #555;">{{ $salaryAdvance->employee->designation->name ?? null }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('employee_id', __('messages.employees.iqama_no')) }}</strong>
                                <p style="color: #555;">{{ $salaryAdvance->employee->iqama_no }}</p>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('employee_id', __('messages.branches.name')) }}</strong>
                                <p style="color: #555;">{{ $salaryAdvance->employee->branch?->name??'' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('employee_id', __('messages.salary_advances.payment_mode')) }}</strong>
                                <p style="color: #555;">{{ $salaryAdvance->account?->account_name??'' }}</p>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('permitted_by', __('messages.salary_advances.permitted_by')) }}</strong>
                                <p style="color: #555;">{{ $salaryAdvance->permittedBy->name }}</p>
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong> {{ Form::label('amount', __('messages.salary_advances.amount')) }}</strong>
                                <p style="color: #555;">{{ $salaryAdvance->amount }}</p>
                            </div>
                        </div>

                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('approved_date', __('messages.salary_advances.approved_date') . ':') }}</strong>
                                <p style="color: #555;">
                                    {{ \Carbon\Carbon::parse($salaryAdvance->approved_date)->format('d M Y') }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>
                                    {{ Form::label('repayment_from', __('messages.salary_advances.repayment_from') . ':') }}</strong>
                                <p style="color: #555;">
                                    {{ \Carbon\Carbon::parse($salaryAdvance->repayment_from)->format('d M Y') }}</p>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('interest_percentage', __('messages.salary_advances.interest_percentage') . ':') }}</strong>
                                <p style="color: #555;">{{ $salaryAdvance->interest_percentage }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('installment_period', __('messages.salary_advances.installment_period') . ':') }}</strong>
                                <p style="color: #555;">{{ $salaryAdvance->installment_period }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('repayment_amount', __('messages.salary_advances.repayment_amount')) }}</strong>
                                <p style="color: #555;">{{ $salaryAdvance->repayment_amount }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>
                                    {{ Form::label('installment', __('messages.salary_advances.installment')) }}</strong>
                                <p style="color: #555;">{{ $salaryAdvance->installment }}</p>
                            </div>
                        </div> --}}

                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <strong>{{ Form::label('status', __('messages.salary_advances.status')) }}</strong>
                                <p style="color: #555;">{{ $salaryAdvance->status ? 'Active' : 'Inactive' }}</p>
                            </div>
                        </div> --}}
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12 mb-0">
                            <strong>{{ Form::label('status', __('messages.salary_advances.description')) }}</strong>
                            <div style="color: #555;"></div>{!! $salaryAdvance->description !!}
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
            let id = $('#loan_id').val();
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
                displayErrorMessage('{{ __('messages.department.select_department') }}');
                return false;
            }
            $.ajax({
                url: route('salary_advances.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('salary_advances.index', );
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
        $(document).ready(function() {
            function updateFields() {
                // Get values from the fields
                var amount = parseFloat($('#amount').val()) || 0;
                var interestPercentage = parseFloat($('#interest_percentage').val()) || 0;
                var repaymentAmount = parseFloat($('#repayment_amount').val()) || 0;
                var installmentPeriod = parseFloat($('#installment_period').val()) || 0;

                // Calculate the repayment amount if it's not set
                if ($('#repayment_amount').val() === '') {
                    repaymentAmount = amount + (amount * (interestPercentage / 100));
                    $('#repayment_amount').val(repaymentAmount.toFixed(2));
                }

                // Calculate the installment amount
                var installment = installmentPeriod > 0 ? (repaymentAmount / installmentPeriod).toFixed(2) : 0;

                // Update the installment field
                $('#installment').val(installment);
            }

            // Attach the function to the keyup event of the fields
            $('#amount, #interest_percentage').on('keyup', function() {
                var amount = parseFloat($('#amount').val()) || 0;
                var interestPercentage = parseFloat($('#interest_percentage').val()) || 0;

                // Calculate repayment amount and update the field
                var repaymentAmount = amount + (amount * (interestPercentage / 100));
                $('#repayment_amount').val(repaymentAmount.toFixed(2));

                // Update installment based on the new repayment amount and period
                updateFields();
            });

            $('#installment_period').on('keyup', function() {
                updateFields();
            });

            $('#employee_select').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
            $('#permittd_by').select2({
                width: '100%', // Set the width of the select element
                allowClear: true // Allow clearing the selection
            });
        });
    </script>
@endsection
