@extends('layouts.app')
@section('title')
    Edit Payment
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1> Edit Payment</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('purchase-invoice-payments.list.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.task-assign.list') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['id' => 'editNewPaymentForm']) }}
                    <input type="hidden" id="paymentId" value="{{ $payment->id }}">
                    <div class="modal-body">
                        <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                        <div class="row">
                            <input type="hidden" name="owner_type" value="App\Models\PurchaseInvoice">
                            {{-- <div class="form-group  col-md-4">
                                {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
                                {{ Form::select('branch_id', $usersBranches ?? [], $payment->branch_id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'branchSelect', 'placeholder' => 'Select Branch']) }}
                            </div> --}}
                            <div class="form-group  col-md-6">

                                {{ Form::label('payment_mode', __('messages.invoice.invoice') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::select('owner_id', $invoices->pluck('estimate_number', 'id') ?? null, $payment->owner_id ?? null, ['class' => 'form-control select2', 'id' => 'owner_id', 'required', 'placeholder' => __('messages.invoice.invoice_number')]) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('amount_received', 'Remaining Amount' . ':') }}
                                {{ Form::number('', 0.0, ['class' => 'form-control price-input', 'id' => 'remainingAmount', 'disabled']) }}
                            </div>
                            <div class="form-group  col-md-6">
                                {{ Form::label('amount_received', __('messages.payment.amount_received') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::number('amount_received', $payment->amount_received ?? null, ['class' => 'form-control price-input', 'required', 'id' => 'paymentAmount']) }}

                            </div>

                            <div class="form-group  col-md-6">
                                {{ Form::label('payment_date', __('messages.payment.payment_date') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::date('payment_date', $payment->payment_date ?? null, ['class' => 'form-control', 'required', 'id' => 'paymentDate']) }}
                            </div>
                            <div class="form-group  col-md-6">
                                {{ Form::label('payment_mode', __('messages.payment.payment_mode') . ':') }}<span
                                    class="required">*</span>
                                {{ Form::select('payment_mode', $paymentModes, $payment->payment_mode ?? null, ['class' => 'form-control select2', 'required', 'id' => 'paymentMode', 'placeholder' => __('messages.placeholder.select_payment_mode')]) }}
                            </div>
                            <div class="form-group  col-md-6">
                                {{ Form::label('transaction_id', __('messages.payment.transaction_id') . ':') }}
                                {{ Form::text('transaction_id', $payment->transaction_id ?? null, ['class' => 'form-control', 'minLength' => '12', 'maxLength' => '18']) }}
                            </div>
                            <div class="form-group  col-md-12 mb-2">
                                {{ Form::label('note', __('messages.payment.note') . ':') }}
                                {{ Form::textarea('note', $payment->note ?? null, ['class' => 'form-control summernote-simple', 'id' => 'note']) }}
                            </div>
                        </div>
                        <div class="text-right mt-2">
                            {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnPaymentSave', 'style' => 'line-height:30px;', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

                        </div>
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
    <script>
        function processingBtn(buttonSelector, state) {
            var $button = $(buttonSelector);
            if (state === 1) {
                // Show loading and disable button
                $button.prop('disabled', true); // Disable the button
                $button.html('processing...'); // Change button text to indicate loading
            } else if (state === 0) {
                // Reset button state
                $button.prop('disabled', false); // Enable the button
                $button.html('Submit'); // Reset button text
            } else if (state === 3) {
                // Reset button state
                $button.prop('disabled', true); // Enable the button
                $button.html('Submit'); // Reset button text
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for dropdowns
            $('.select2').select2();

            // Submit the form via AJAX
            $('#editNewPaymentForm').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize();
                processingBtn('#btnPaymentSave', 1);


                let paymentId = $('#paymentId').val();
                let updateUrl = route('purchase-invoice-payments.list.update', paymentId);

                $.ajax({
                    url: updateUrl,
                    type: 'PUT',
                    data: formData,
                    success: function(response) {
                        displaySuccessMessage(response.message);
                        const url = route(
                            'purchase-invoice-payments.list.index');
                        window.location.href = url;
                        processingBtn('#btnPaymentSave', 0);
                    },
                    error: function(xhr) {
                        processingBtn('#btnPaymentSave', 0);
                        displayErrorMessage(xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>

    <script>
        // Store invoices data in JSON format
        const invoicesData = @json($invoices);

        // Function to update the owner_id select box based on selected branch_id
        function updateInvoicesByBranch() {
            const branchId = $('#branchSelect').val(); // Get the selected branch_id
            const ownerSelect = $('#owner_id'); // The owner_id select box

            // Clear the current options in the owner_id dropdown
            ownerSelect.empty();

            // Check if any invoices exist for the selected branch
            const filteredInvoices = invoicesData.filter(invoice => invoice.branch_id == branchId);

            // If there are invoices, populate the dropdown with invoice numbers
            if (filteredInvoices.length > 0) {
                filteredInvoices.forEach(invoice => {
                    ownerSelect.append(new Option(invoice.invoice_number, invoice.id));

                });
            } else {
                // If no invoices are found, show a placeholder option
                ownerSelect.append(new Option('No invoices available', ''));
            }

            // Reinitialize the select2 plugin to apply the changes
            ownerSelect.trigger('change');

        }

        // Handle the change event
        $('#owner_id').on('change', function() {
            // Get the selected value
            let selectedValue = $(this).val();
            var thisInvoice = invoicesData.find(invoice => invoice.id == selectedValue);
            $("#remainingAmount").val(thisInvoice?.remaining_amount.toFixed(2) ?? 0)
        });

        // Bind change event to the branchSelect dropdown
        $('#branchSelect').on('change', updateInvoicesByBranch);
        $('#owner_id').trigger('change');
        // Call the function on page load to populate the owner_id dropdown based on the initial branch (if any)
        // updateInvoicesByBranch();
    </script>
@endsection
