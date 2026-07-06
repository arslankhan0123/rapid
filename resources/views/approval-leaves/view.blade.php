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
            <div class="row">
                @if ($application->status == 0)
                    <div class="col pr-1">
                        <button class="btn btn-success action-btn has-icon approve-btn "
                            style="width: 150px;height:40px;font-size:15px;" data-id="{{ $application->id }}">
                            <i class="fa fa-check"></i>Approve
                        </button>
                    </div>
                @endif


                <div class="col pl-0">
                    <a href="{{ route('approval-leaves.index') }}" class="btn btn-primary"
                        style="line-height: 30px;">{{ __('messages.leave-applications.list') }}</i>
                    </a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('end_date', __('messages.branches.name')) }}</strong>
                                    <p style="color: #555;">{{ $application->branch?->name ?? '' }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="form-group">
                                    <strong>{{ Form::label('employee_id', __('messages.employees.id')) }}</strong>
                                    <p style="color: #555;">{{ $application->employee->iqama_no ?? '' }}</p>
                                </div>

                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="form-group">
                                    <strong>{{ Form::label('employee_id', __('messages.leave-applications.employee')) }}</strong>
                                    <p style="color: #555;">{{ $application->employee->name }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="form-group">
                                    <strong>{{ Form::label('employee_id', __('messages.designations.name')) }}</strong>
                                    <p style="color: #555;">{{ $application->employee->designation->name ?? '' }}</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>{{ Form::label('from_date', __('messages.leave-applications.from_date')) }}</strong>
                                    <p style="color: #555;">{{ $application->from_date }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('end_date', __('messages.leave-applications.end_date')) }}</strong>
                                    <p style="color: #555;">{{ $application->end_date }}</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('total_days', __('messages.leave-applications.total_days')) }}</strong>
                                    <p style="color: #555;">{{ $application->total_days }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('leave_id', __('messages.leave-applications.leave_type')) }}</strong>
                                    <p style="color: #555;">{{ $application->leave->name }}</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>{{ Form::label('hard_copy', __('messages.leave-applications.hard_copy')) }}</strong>
                                    <p>
                                        <a target="_blank"
                                            href="/uploads/public/leave_applications/{{ rawurlencode($application->hard_copy) }}">
                                            {{ $application->hard_copy }}
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('leave_id', 'Status') }}</strong>
                                    <p
                                        style="color: {{ $application->status ? '#28a745' : '#dc3545' }}; font-weight: bold;">
                                        {{ $application->status ? 'Approved' : 'Pending' }}
                                    </p>

                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('leave_id', 'Approved By') }}</strong>
                                    <p style="color: #555;">{{ $application->approvedBy?->fullName ?? '' }}</p>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12">
                                <div class="form-group">
                                    <strong>
                                        {{ Form::label('title', __('messages.leave-applications.reason')) }}</strong>
                                    {!! $application->description !!}
                                </div>
                            </div>


                        </div>
                        {{ Form::open(['id' => 'editFormNew', 'enctype' => 'multipart/form-data']) }}
                        {{ Form::hidden('id', $application->id, ['id' => 'leave_application_id']) }}

                        <div class="row">
                            <div class="col-lg-6 col-md-6 other_field">
                                <div class="form-group">
                                    {{ Form::label('paid Leave Days', __('messages.leave-applications.paid_leave_days')) }}<span
                                        class="required">*</span>
                                    {{ Form::number('paid_leave_days', $application->paid_leave_days ?? 0, ['class' => 'form-control', 'id' => 'paid_leave_days', 'required', 'autocomplete' => 'off']) }}
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 other_field">
                                <div class="form-group">
                                    {{ Form::label('paid Leave Days', __('messages.leave-applications.paid_leave_amount')) }}
                                    {{ Form::number('paid_leave_amount', $application->paid_leave_amount ?? 0, ['class' => 'form-control', 'id' => 'paid_leave_amount', 'required', 'autocomplete' => 'off', 'readonly']) }}
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 other_field">
                                <div class="form-group">
                                    {{ Form::label('paid Leave Days', __('messages.leave-applications.ticket_amount')) }}
                                    {{ Form::number('ticket_amount', $application->ticket_amount ?? 0, ['class' => 'form-control', 'id' => 'ticket_amount', 'required', 'autocomplete' => 'off']) }}
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 other_field">
                                <div class="form-group">
                                    {{ Form::label('paid Leave Days', __('messages.leave-applications.claim_amount')) }}
                                    {{ Form::number('claim_amount', $application->claim_amount ?? 0, ['class' => 'form-control', 'id' => 'claim_amount', 'required', 'autocomplete' => 'off', 'readonly']) }}
                                </div>
                            </div>

                        </div>

                        <div class="text-right mr-1">
                            {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

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
        $(document).on('click', '.approve-btn', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            swal({
                    title: 'Are you sure!!',
                    text: "Do you want to approve this leave application?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    showConfirmButton: true,
                    confirmButtonColor: '#3085d6', // Optional: Change confirm button color
                    cancelButtonColor: '#d33', // Optional: Change cancel button color
                },
                function() {
                    approveLeave(id);
                });

        });

        // Function to handle approval action
        function approveLeave(id) {
            startLoader();
            $.ajax({
                url: `{{ route('approval-leaves.update', ':id') }}`.replace(':id', id),
                method: 'get',
                success: function(response) {
                    displaySuccessMessage("leave Application Approved");
                    window.location.href = "{{ route('approval-leaves.index') }}";

                },
                error: function(response) {
                    displayErrorMessage("Failed to Update");
                    window.location.href = "{{ route('approval-leaves.index') }}";

                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {

            var employees = @json($employees);
            var totalLeaves = @json($totalLeaves);
            var application = @json($application);


            $("#paid_leave_days").on('input', function() {
                var paidLeaveDays = parseFloat($(this).val()) || 0;


                var employee = @json($application->employee);

                if (employee) {
                    var paidLeaveAmount = paidLeaveDays * (employee.basic_salary / 30);
                    $('#paid_leave_amount').val(paidLeaveAmount.toFixed(2));
                } else {
                    alert("Select Employee First");
                    $('#paid_leave_amount').val(0);
                    return;
                }
            });

            $('#ticket_amount').keyup(function() {
                var ticketAmount = parseFloat($(this).val()) || 0;
                var paidLeaveAmount = parseFloat($('#paid_leave_amount').val()) || 0;
                var claimAmount = ticketAmount + paidLeaveAmount;
                $('#claim_amount').val(claimAmount.toFixed(2));
            });

            toggleDateFields(); // initial load
            function toggleDateFields() {
                let selectedText = application['leave']['name'];
                console.log(selectedText);
                if (selectedText === 'Annual Leave') {
                    $(".other_field").show();
                    $('#ticket_amount').prop('required', true);
                } else {
                    $(".other_field").hide();
                    $('#ticket_amount').prop('required', false);
                }
            }




            $('#leave_type').on('change', function() {
                toggleDateFields(); // on change
            });



            $(document).on('submit', '#editFormNew', function(e) {
                e.preventDefault();
                processingBtn('#editFormNew', '#btnSave', 'loading');
                let id = $('#leave_application_id').val();
                var formData = new FormData(this);
                $.ajax({
                    type: 'post',
                    url: route('leave-applications.annual.update', id),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.success) {
                            displaySuccessMessage("Saved");
                            const url = route('approval-leaves.index');
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
        });
    </script>
@endsection
