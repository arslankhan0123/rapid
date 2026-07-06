@extends('layouts.app')
@section('title')
    {{ __('messages.certificate.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.certificate.edit') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('certificate.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.assets.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                        {{ Form::open(['id' => 'editForm']) }}
                        {{ Form::hidden('id', $category->id, ['id' => 'category_id']) }}
                        @include('certificate.edit_fields')
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
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        $(document).on('submit', '#editForm', function(event) {
            event.preventDefault();
            processingBtn('#editForm', '#btnSave', 'loading');
            let id = $('#category_id').val();

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
                url: route('certificate.update', id),
                type: 'put',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('certificate.index', );
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
            $('#customerSelectBox').select2({
                width: '100%', // Set the width of the select element
                allowClear: true, // Allow clearing the selection
                placeholder: 'Select Client'
            });
            $('#deliveredBySelectBox').select2({
                width: '100%', // Set the width of the select element
                allowClear: true, // Allow clearing the selection
                placeholder: 'Select One'
            });
            $('#receivedBySelectBox').select2({
                width: '100%', // Set the width of the select element
                allowClear: true, // Allow clearing the selection
                placeholder: 'Select One'
            });

            // Event listener for when a customer is selected from the dropdown
            $('#customerSelectBox').on('change', function() {
                var customerId = $(this).val();
                var customerName = $('#customerSelectBox option:selected').text();
                $('#customerNameInput').val(customerName);

                // var customer=customers.find(c=>c.id==customerId);
                // $('#vendor_code').val(customer?.vendor_code??'');
                // $(this).select2('close');
            });
        })
        $('.datepicker').datetimepicker({
            format: 'DD-MM-YYYY', // Date format (adjust as needed)
            useCurrent: false, // Prevents using the current date by default
            showClose: true, // Show a "close" button
            showClear: true, // Show a "clear" button
            showTodayButton: true, // Show a "today" button
            icons: {
                time: 'fas fa-clock',
                date: 'fas fa-calendar',
                up: 'fas fa-chevron-up',
                down: 'fas fa-chevron-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right',
                today: 'fas fa-check-circle',
                clear: 'fas fa-trash',
                close: 'fas fa-times',
            },
        });
        $('.timepicker').datetimepicker({
            format: 'HH:mm', // 24-hour format
            useCurrent: false, // Prevent default selection of current time
            showClose: true, // Show the close button
            showClear: true, // Show the clear button
            showTodayButton: true, // Show the "Today" button
            icons: {
                time: 'fas fa-clock',
                date: 'fas fa-calendar',
                up: 'fas fa-chevron-up',
                down: 'fas fa-chevron-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right',
                today: 'fas fa-check-circle',
                clear: 'fas fa-trash',
                close: 'fas fa-times',
            },
            stepping: 1, // Time steps in minutes
            ignoreReadonly: true // Allow interaction even if input is readonly
        });
    </script>
@endsection
