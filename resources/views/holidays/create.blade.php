@extends('layouts.app')
@section('title')
    {{ __('messages.holidays.add') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.holidays.add') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('holidays.index') }}" class="btn btn-primary form-btn">List</a>
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
                                    {{ Form::label('name', __('messages.holidays.holiday_name') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'holiday_name', 'autocomplete' => 'off']) }}
                                </div>

                                <div class="form-group col-sm-12">
                                    {{ Form::label('from_date', __('messages.holidays.from_date') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::date('from_date', null, ['class' => 'form-control', 'required', 'id' => 'from_date']) }}
                                </div>

                                <div class="form-group col-sm-12">
                                    {{ Form::label('end_date', __('messages.holidays.end_date') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::date('end_date', null, ['class' => 'form-control', 'required', 'id' => 'end_date']) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('end_date', __('messages.holidays.total_days') . ':') }}
                                    {{ Form::text(null, null, ['class' => 'form-control', 'required', 'id' => 'total_days', 'disabled']) }}
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
        let departmentNewCreateUrl = route('holidays.store');
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
                        const url = route('holidays.index', );
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
            function calculateDays() {
                var fromDate = new Date($('#from_date').val());
                var endDate = new Date($('#end_date').val());

                // Check if both dates are valid
                if (!isNaN(fromDate.getTime()) && !isNaN(endDate.getTime())) {
                    var timeDiff = endDate.getTime() - fromDate.getTime();
                    var dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // Add 1 to include the end date

                    if (dayDiff < 0) {
                        $('#total_days').val('');
                        $('#btnSave').prop('disabled', true); // Disable button if days count is negative
                    } else {
                        $('#total_days').val(dayDiff);
                        $('#btnSave').prop('disabled', false); // Enable button if days count is non-negative
                    }
                } else {
                    $('#total_days').val(''); // Clear total_days if dates are not valid
                    $('#btnSave').prop('disabled', true); // Disable button if dates are not valid
                }
            }

            // Attach event handlers to update total_days when from_date or end_date changes
            $('#from_date, #end_date').on('change', calculateDays);

            // Optionally, calculate days initially if dates are pre-filled
            calculateDays();
        });
    </script>
@endsection
