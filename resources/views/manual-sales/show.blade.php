@extends('layouts.app')
@section('title')
    {{ __('messages.invoice.invoice_details') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/@fortawesome/fontawesome-free/css/all.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/css/sales/view-as-customer.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/css/invoices/invoices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/int-tel/css/intlTelInput.css') }}">
    <style>
        .round-off-column {
            display: {{ $isRound ? 'table-cell' : 'none' }};
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.invoice.invoice_details') }}</h1>
            <div class="section-header-breadcrumb float-right">
                @if (
                    $invoice->payment_status !== \App\Models\Invoice::STATUS_PAID &&
                        $invoice->payment_status !== \App\Models\Invoice::STATUS_PARTIALLY_PAID &&
                        $invoice->payment_status !== \App\Models\Invoice::STATUS_CANCELLED)
                    <a href="{{ route('manual-sales.edit', ['invoice' => $invoice->id]) }}"
                        class="btn btnWarning text-white mr-2 form-btn">{{ __('messages.common.edit') }}
                    </a>
                @endif
                <a href="{{ route('manual-sales.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.common.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @include('manual-sales.show_fields')
                </div>
            </div>
        </div>
        @include('manual-sales.send_modals')


    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('assets/js/int-tel/js/intlTelInput.min.js') }}"></script>
    <script src="{{ asset('assets/js/int-tel/js/utils.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let invoiceUrl = "{{ route('manual-sales.index') }}";
        let invoiceId = "{{ $invoice->id }}";
        let ownerType = 'App\\Models\\Invoice';
        let changeStatus = "{{ route('invoice.change-status', $invoice->id) }}";
        let taskUrl = "{{ route('tasks.index') }}";
        let statusArray = JSON.parse('@json($status)');
        let priorities = JSON.parse('@json($priorities)');
        let ownerId = "{{ $invoice->id }}";
        let authId = '{{ Auth::id() }}';
        let ownerUrl = "{{ route('invoices.index') }}";
        let memberUrl = "{{ route('members.index') }}";
        let addNote = "{{ __('messages.placeholder.add_note') }}";
    </script>
    <script src="{{ mix('assets/js/notes/new-notes.js') }}"></script>
    <script src="{{ mix('assets/js/reminder/reminder.js') }}"></script>
    <script src="{{ mix('assets/js/payments/payments.js') }}"></script>
    <script src="{{ mix('assets/js/custom/get-price-format.js') }}"></script>
    <script src="{{ mix('assets/js/payments/add-payment.js') }}"></script>
    <script src="{{ mix('assets/js/invoices/show-page.js') }}"></script>
    <script src="{{ mix('assets/js/tasks/tasks.js') }}"></script>
    <script>
        var input = document.querySelector("#phoneNumber");
        var iti = window.intlTelInput(input, {
            initialCountry: "ae", // Set default country to Dubai (UAE)
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            nationalMode: false // Ensure country code is visible and used
        });


        $("#btnSend").click(function() {
            $("#sendEmailModal").modal('show');
        });
        $("#btnSendWhatsapp").click(function() {
            $("#sendWhatsAppModal").modal('show');
        });
        $("#btnSendSMS").click(function() {
            $("#sendSMSModal").modal('show');
        });

        function progressBTN(button, isLoading) {
            if (isLoading) {
                button.prop('disabled', true); // Disable the button
                button.html('Sending... <i class="fa fa-spinner fa-spin"></i>'); // Show loading spinner
            } else {
                button.prop('disabled', false); // Enable the button
                button.html('Send'); // Reset button text
            }
        }

        $(document).ready(function() {
            $('#btnSendEmailPOST').on('click', function(e) {
                e.preventDefault();

                var ccEmail = $('#cc').val();
                var bccEmail = $('#bcc').val();
                var invoiceNumber =
                    '{{ $invoice->invoice_number }}'; // The invoice number from your backend
                progressBTN($('#btnSendEmailPOST'), true);
                // Make the AJAX call
                $.ajax({
                    url: '{{ route('invoices.send.email') }}', // Your route to handle email sending
                    method: 'POST',
                    data: {
                        ccEmail: ccEmail,
                        invoiceNumber: invoiceNumber,
                        bccEmail: bccEmail,
                    },
                    success: function(response) {
                        $("#sendEmailModal").modal('hide');
                        // Handle success
                        if (response.message) {
                            displaySuccessMessage(response.message);
                        }
                        progressBTN($('#btnSendEmailPOST'), false);
                        //  $('#sendEmailForm')[0].reset(); // Reset the form after success
                    },
                    error: function(xhr, status, error) {
                        progressBTN($('#btnSendEmailPOST'), false);
                        displayErrorMessage("Failed to send");
                        $("#sendEmailModal").modal('hide');
                    }
                });
            });


            //below sms
            $('#btnSendSMSPOST').on('click', function(e) {
                e.preventDefault();

                var phone = $('#phoneNumber').val();
                var invoiceNumber =
                    '{{ $invoice->invoice_number }}'; // The invoice number from your backend
                progressBTN($('#btnSendSMSPOST'), true);
                var smsData = $("#smsData").val();
                // Make the AJAX call
                $.ajax({
                    url: '{{ route('invoices.send.sms') }}', // Your route to handle email sending
                    method: 'POST',
                    data: {
                        invoiceNumber: invoiceNumber,
                        phone: phone,
                        smsData: smsData
                    },
                    success: function(response) {


                        $("#sendSMSModal").modal('hide');
                        // Handle success
                        if (response.message) {
                            displaySuccessMessage(response.message);
                        }
                        progressBTN($('#btnSendSMSPOST'), false);

                    },
                    error: function(xhr, status, error) {
                        // Check if the response contains a JSON error message
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            // Display the error message from the server
                            displayErrorMessage(xhr.responseJSON.error);
                        } else {
                            // Fallback error message if the server doesn't return an error message
                            displayErrorMessage("Failed to send");
                        }

                        // Hide the progress button and modal
                        progressBTN($('#btnSendSMSPOST'), false);
                        $("#sendSMSModal").modal('hide');
                    }

                });
            });

            //below sms
            $('#btnSendWhatsppPost').on('click', function(e) {
                e.preventDefault();

                var phone = $('#whatsappto').val();
                var invoiceNumber =
                    '{{ $invoice->invoice_number }}'; // The invoice number from your backend
                var whatsappBody = $("#whatsappBody").val();


                if (phone.length == 0) {
                    displayErrorMessage("Phone number is required.");
                    return; // Exit the function if phone is empty, but do not hide the modal
                }

                progressBTN($('#btnSendWhatsppPost'), true);

                // Make the AJAX call
                $.ajax({
                    url: '{{ route('invoices.send.whatsapp') }}', // Your route to handle SMS sending
                    method: 'POST',
                    data: {
                        invoiceNumber: invoiceNumber,
                        phone: phone,
                        whatsappBody: whatsappBody
                    },
                    success: function(response) {
                        $("#sendWhatsAppModal").modal('hide');
                        // Handle success
                        if (response.message) {
                            displaySuccessMessage(response.message);
                        }
                        progressBTN($('#btnSendWhatsppPost'), false);
                    },
                    error: function(xhr, status, error) {
                        // Check if the response contains a JSON error message
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            // Display the error message from the server
                            displayErrorMessage(xhr.responseJSON.error);
                        } else {
                            // Fallback error message if the server doesn't return an error message
                            displayErrorMessage("Failed to send");
                        }

                        // Hide the progress button but keep the modal open
                        progressBTN($('#btnSendWhatsppPost'), false);
                    }
                });
            });



        });
    </script>
@endsection
