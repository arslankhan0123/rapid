@extends('layouts.app')
@section('title')
    {{ __('messages.company_loans.add_company_loan') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
<style>
    .select2-container {
        width: 100% !important;
    }
</style>
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.company_loans.add_company_loan') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('company-loans.index') }}" class="btn btn-primary form-btn float-right">
                    {{ __('messages.common.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('layouts.errors')
                    {!! Form::open(['route' => 'company-loans.store', 'id' => 'addCompanyLoanForm']) !!}
                    @include('company_loans.fields')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let getCustomersUrl = "{{ route('company-loans.get-customers-by-type') }}";
    </script>
    <script>
        // Remove the mix include line and put the JavaScript directly here
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2({
                width: '100%'
            });
            // Function to handle type change
            function handleTypeChange(type) {
                // Hide all fields first
                $('#customerField').hide();
                $('#supplierField').hide();
                $('#personalField').hide();

                // Clear required attributes and values
                $('#customerSelect').removeAttr('required').val(null).trigger('change');
                $('#supplierSelect').removeAttr('required').val(null).trigger('change');
                $('#nameInput').removeAttr('required').val('');

                // Show relevant field based on type
                if (type === 'customer') {
                    $('#customerField').show();
                    $('#customerSelect').attr('required', 'required');
                    $('#customerSelect').select2('destroy').select2({
                        width: '100%'
                    }); // Fix width
                } else if (type === 'supplier') {
                    $('#supplierField').show();
                    $('#supplierSelect').attr('required', 'required');
                    $('#supplierSelect').select2('destroy').select2({
                        width: '100%'
                    }); // Fix width
                } else if (type === 'personal') {
                    $('#personalField').show();
                    $('#nameInput').attr('required', 'required');
                }
            }

            // Handle initial type
            let initialType = $('#typeSelect').val();
            if (initialType) {
                handleTypeChange(initialType);
            }

            // Handle type change event
            $('#typeSelect').on('change', function() {
                let type = $(this).val();
                handleTypeChange(type);
            });

            // For edit page, pre-select values
            if (typeof companyLoanType !== 'undefined') {
                $('#typeSelect').val(companyLoanType).trigger('change');

                // Set timeout to allow fields to show first
                setTimeout(function() {
                    if (companyLoanType === 'customer' && typeof companyLoanCustomerId !== 'undefined') {
                        $('#customerSelect').val(companyLoanCustomerId).trigger('change');
                    } else if (companyLoanType === 'supplier' && typeof companyLoanSupplierId !==
                        'undefined') {
                        $('#supplierSelect').val(companyLoanSupplierId).trigger('change');
                    } else if (companyLoanType === 'personal' && typeof companyLoanName !== 'undefined') {
                        $('#nameInput').val(companyLoanName);
                    }
                }, 100);
            }

            // Auto calculate number of days
            function calculateDays() {
                let receivedDate = $('#loan_received_date').val();
                let refundDate = $('#loan_refund_date').val();

                if (receivedDate && refundDate) {
                    let start = new Date(receivedDate);
                    let end = new Date(refundDate);

                    // Calculate difference in milliseconds
                    let diffTime = end - start;

                    if (diffTime >= 0) {
                        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        $('#number_of_days').val(diffDays); // set value in input
                    } else {
                        $('#number_of_days').val('');
                    }
                }
            }

            // Trigger calculation on change
            $('#loan_received_date, #loan_refund_date').on('change', calculateDays);


            // Form submission handling
            $('#addCompanyLoanForm, #editCompanyLoanForm').on('submit', function() {
                let type = $('#typeSelect').val();

                // Clear validation messages
                $('.invalid-feedback').remove();
                $('.is-invalid').removeClass('is-invalid');

                // Remove required attributes from all fields first
                $('#customerSelect').removeAttr('required');
                $('#supplierSelect').removeAttr('required');
                $('#nameInput').removeAttr('required');

                // Add required attribute only to the relevant field
                if (type === 'customer') {
                    $('#customerSelect').attr('required', 'required');
                } else if (type === 'supplier') {
                    $('#supplierSelect').attr('required', 'required');
                } else if (type === 'personal') {
                    $('#nameInput').attr('required', 'required');
                }
            });
        });
    </script>
@endsection
