@extends('layouts.app')
@section('title')
    {{ __('messages.rentals.rentals') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <!-- Add these in your layout or blade file -->

    <style>
        #statusSwitch.form-check-input {
            width: 3em;
            height: 1.5em;
        }

        #statusSwitch.form-check-input:checked {
            background-color: #0d6efd;
            /* Change the color as needed */
        }

        #statusSwitch.form-check-input::before {
            width: 1.5em;
            height: 1.5em;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.rentals.add') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('rentals.index') }}" class="btn btn-primary form-btn">{{ __('messages.rentals.list') }}
                </a>
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
                                <div class="form-group col-sm-12">
                                    {{ Form::label('supplier_id', __('messages.rentals.select_supplier') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('supplier_id', $suppliers, null, ['class' => 'form-control', 'required', 'id' => 'supplier_select', 'placeholder' => __('messages.rentals.select_supplier')]) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('start_date', __('messages.rentals.start_date') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::input('date', 'start_date', null, ['class' => 'form-control', 'required', 'id' => 'start_date', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('end_date', __('messages.rentals.end_date') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::input('date', 'end_date', null, ['class' => 'form-control', 'required', 'id' => 'end_date', 'autocomplete' => 'off']) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('type', __('messages.rentals.type') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('type', ['hourly' => 'Hourly', 'daily' => 'Daily', 'monthly' => 'Monthly'], null, ['class' => 'form-control', 'required', 'id' => 'type_select', 'placeholder' => __('messages.rentals.type')]) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('type', __('messages.rentals.amount') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::number('amount', null, ['class' => 'form-control', 'id' => 'amount', 'required']) }}
                                </div>

                                <div class="form-group col-sm-12">
                                    {{ Form::label('type', __('messages.rentals.tax') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::select('tax_id', $taxRates, null, ['class' => 'form-control', 'required', 'id' => 'tax_select', 'placeholder' => __('messages.rentals.select_tax')]) }}
                                </div>

                                <div class="form-group col-sm-12">
                                    {{ Form::label('type', __('messages.rentals.tax_amount') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::number('tax_amount', null, ['class' => 'form-control', 'id' => 'tax_amount', 'required', 'readonly']) }}
                                </div>

                                <div class="form-group col-sm-12">
                                    {{ Form::label('type', __('messages.rentals.rent_including_tax') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::number('total_rent_amount', null, ['class' => 'form-control', 'id' => 'rent_including_tax', 'required', 'readonly']) }}
                                </div>
                                <div class="form-group col-sm-12 mb-0">
                                    {{ Form::label('description', __('messages.rentals.description') . ':') }}
                                    {{ Form::textarea('description', null, ['class' => 'form-control summernote-simple', 'id' => 'description']) }}
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
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        let departmentNewCreateUrl = route('rentals.store');
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
                        const url = route('rentals.index', );
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


        $(document).ready(function() {

            $('#supplier_select').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.rentals.select_supplier') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });
            $('#tax_select').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.rentals.select_tax') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });
            $('#type_select').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.rentals.type') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            var allTaxes = @json($allTaxes);

            function updateAmounts() {
                var selectedId = $('#tax_select').val();
                var selectedTaxRate = allTaxes.find(tax => tax.id == selectedId);
                var amount = parseFloat($('#amount').val()) || 0;

                if (selectedTaxRate) {
                    var taxRate = parseFloat(selectedTaxRate.tax_rate) || 0;
                    var taxAmount = (amount * (taxRate / 100)).toFixed(2);
                    var rentIncludingTax = (parseFloat(amount) + parseFloat(taxAmount)).toFixed(2);

                    $('#tax_amount').val(taxAmount);
                    $('#rent_including_tax').val(rentIncludingTax);
                } else {
                    $('#tax_amount').val('');
                    $('#rent_including_tax').val('');
                }
            }

            $('#tax_select').on('change', updateAmounts);
            $('#amount').on('input', updateAmounts);
        });
    </script>
@endsection
