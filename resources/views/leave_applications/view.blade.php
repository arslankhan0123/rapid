@extends('layouts.app')
@section('title')
    {{ __('messages.leave-applications.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.leave-applications.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('leave-applications.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.leave-applications.list') }}</i>
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 text-right mb-3">
                                <a href="{{ route('leave-applications.pdf', ['leaveApplication' => $leaveApplication->id]) }}"
                                    class="btn btn-info">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('end_date', __('messages.branches.name')) }}</strong>
                                    <p style="color: #555;">{{ $leaveApplication->branch?->name ?? '' }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="form-group">
                                    <strong>{{ Form::label('employee_id', __('messages.employees.id')) }}</strong>
                                    <p style="color: #555;">{{ $leaveApplication->employee->iqama_no ?? '' }}</p>
                                </div>

                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="form-group">
                                    <strong>{{ Form::label('employee_id', __('messages.leave-applications.employee')) }}</strong>
                                    <p style="color: #555;">{{ $leaveApplication->employee->name }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="form-group">
                                    <strong>{{ Form::label('employee_id', __('messages.designations.name')) }}</strong>
                                    <p style="color: #555;">{{ $leaveApplication->employee->designation->name ?? '' }}</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>{{ Form::label('from_date', __('messages.leave-applications.from_date')) }}</strong>
                                    <p style="color: #555;">{{ $leaveApplication->from_date }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('end_date', __('messages.leave-applications.end_date')) }}</strong>
                                    <p style="color: #555;">{{ $leaveApplication->end_date }}</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('total_days', __('messages.leave-applications.total_days')) }}</strong>
                                    <p style="color: #555;">{{ $leaveApplication->total_days }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('leave_id', __('messages.leave-applications.leave_type')) }}</strong>
                                    <p style="color: #555;">{{ $leaveApplication->leave->name }}</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('total_days', __('messages.leave-applications.paid_leave_days')) }}</strong>
                                    <p style="color: #555;">{{ number_format($leaveApplication->paid_leave_days, 2) }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('total_days', __('messages.leave-applications.paid_leave_amount')) }}</strong>
                                    <p style="color: #555;">{{ $leaveApplication->paid_leave_amount }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('total_days', __('messages.leave-applications.ticket_amount')) }}</strong>
                                    <p style="color: #555;">{{ $leaveApplication->ticket_amount }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('total_days', __('messages.leave-applications.claim_amount')) }}</strong>
                                    <p style="color: #555;">{{ $leaveApplication->claim_amount }}</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>{{ Form::label('hard_copy', __('messages.leave-applications.hard_copy')) }}</strong>
                                    <p>
                                        <a target="_blank"
                                            href="/uploads/public/leave_applications/{{ rawurlencode($leaveApplication->hard_copy) }}">
                                            {{ $leaveApplication->hard_copy }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('title', __('messages.leave-applications.reason')) }}</strong>
                                    {!! $leaveApplication->description !!}
                                </div>
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
        $(document).on('submit', '#editFormNew', function(e) {
            e.preventDefault();
            processingBtn('#editFormNew', '#btnSave', 'loading');
            let id = $('#leave_application_id').val();
            var formData = new FormData(this);
            $.ajax({
                type: 'post',
                url: route('leave-applications.update', id),
                data: formData,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success) {
                        const url = route('leave-applications.index');
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                    processingBtn('#editFormNew', '#btnSave');
                },
                complete: function() {
                    processingBtn('#editFormNew', '#btnSave');
                },
            });
        });



        $(document).ready(function() {

            function calculateTotalDays() {
                var fromDate = $('#from_date').val();
                var endDate = $('#end_date').val();

                if (fromDate && endDate) {
                    // Convert the date strings into Date objects
                    var start = new Date(fromDate);
                    var end = new Date(endDate);

                    // Calculate the difference in time
                    var differenceInTime = end.getTime() - start.getTime();

                    // Convert the difference in time to days
                    var differenceInDays = differenceInTime / (1000 * 3600 * 24);

                    // Include both start and end dates
                    var totalDays = differenceInDays + 1;

                    // Set the total days in the input field
                    $("#total_days").val(totalDays);

                    // Check if total days is valid (greater than 0)
                    if (totalDays > 0) {
                        $('#btnSave').prop('disabled', false); // Enable the button
                    } else {
                        $('#btnSave').prop('disabled', true); // Disable the button
                    }
                } else {
                    $('#btnSave').prop('disabled', true); // Disable the button if dates are not selected
                }
            }
            // Trigger the calculation when the date fields change
            $('#from_date, #end_date').on('change', function() {
                calculateTotalDays();
            });

            $('#select_employee').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.leave-applications.select_employee') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });
            $('#leave_type').select2({
                width: '100%', // Set the width of the select element
                placeholder: '{{ __('messages.leave-applications.leave_type') }}', // Placeholder text
                allowClear: true // Allow clearing the selection
            });


        });
    </script>
@endsection
