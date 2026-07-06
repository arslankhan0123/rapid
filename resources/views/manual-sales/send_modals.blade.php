<style>
    .modal-header-options {
        display: flex;
        align-items: center;
        gap: 10px;
        /* Adjust the gap between elements */
    }

    .modal-header-options label {
        display: flex;
        align-items: center;

        /* Adjust text size */
    }

    .modal-header {
        padding: 16px;
    }

    .modal-header-options input[type="radio"] {
        width: 20px;
        /* Set the size of the radio button */
        height: 20px;
        /* Set the size of the radio button */
        margin-right: 5px;
        /* Add space between radio button and label text */
        cursor: pointer;
    }

    .modal-header {
        background: #d9f0ff;
    }

    .txtColor {
        color: #25a5ee;
    }

    .closeBTN {
        color: #25a5ee !important;

    }

    #phoneNumber {
        padding-left: 68px;
        /* Adjust padding to fit the flag and country code */
        padding-right: 10px;
        /* Adjust as necessary */
        height: 40px;
        /* Adjust the height as necessary */
    }
</style>
<div id="sendEmailModal" class="modal fade address-modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header ">
                <h5 class="modal-title txtColor "> <i class="fa fa-envelope mr-2" aria-hidden="true"></i>Send To Email</h5>
                <div class="modal-header-options">
                    <label>
                        <input type="radio" name="email-option" id="summary-radio" checked>
                        <span class="txtColor ">Summary</span>
                    </label>
                    <label>
                        <input type="radio" name="email-option" id="details-radio">
                        <span class="txtColor">Details</span>
                    </label>
                    <button type="button" aria-label="Close" class="close closeBTN" data-dismiss="modal"> <i
                            class="fa fa-times-circle" aria-hidden="true"></i></button>
                </div>

            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                <form id="sendEmailForm">
                    <div class="row">
                        <!-- To Field -->
                        <div class="mb-1 col-md-6">
                            <label for="toEmail" class="form-label">From</label>
                            <input type="email" class="form-control" value="{{ $settings['email'] ?? '' }}" required
                                readonly>
                        </div>
                        <div class="mb-1 col-md-6">
                            <label for="toEmail" class="form-label">To</label>
                            <input type="email" class="form-control" id="toEmail" placeholder="Recipient's email"
                                value="{{ $invoice->customer->email ?? '' }}" required readonly>
                        </div>
                        <div class="mb-1 col-md-6">
                            <label for="subject" class="form-label">CC</label>
                            <input type="text" class="form-control" id="cc" name="cc">
                        </div>
                        <div class="mb-1 col-md-6">
                            <label for="subject" class="form-label">BCC</label>
                            <input type="text" class="form-control" id="bcc" name="bcc">
                        </div>
                        <!-- Subject Field -->
                        <div class="mb-1 col-md-12">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" value="Invoice-1475" id="subject"
                                placeholder="Email subject" required readonly>
                        </div>

                        <!-- Body Field -->
                        <div class="mb-1  col-md-12">
                            <label for="emailBody" class="form-label">Body</label>
                            <textarea class="form-control" id="emailBody" style="height: 130px;" placeholder="Type your message here" readonly
                                required>URL : {{ url('/admin/invoices/' . $invoice->invoice_number) }}

Invoice Number : {{ $invoice->invoice_number }}
Total Before Discount: {{ number_format($subtotal, 2) }} SAR
Discount {{ isset($invoice->discount_type) ? ($invoice->discount_type == 0 ? '%' : '$') : ' ' }}: {{ number_format($invoice->discount ?? 0, 2) }}
Total After Discount: {{ number_format($totalTaxable, 2) }} SAR
Total VAT: {{ number_format($totalVat, 2) }} SAR
Net Total: {{ number_format($invoice->total_amount, 2) }} SAR
</textarea>
                        </div>
                    </div>




                    <!-- Invoice Row -->
                    <div class="d-flex align-items-center p-1"
                        style="background-color: #f8f9fa; border: 1px solid #ddd; height: 30px;">
                        <span class="text-truncate">📄 <a href="/path/to/invoice.pdf"
                                target="_blank">{{ 'INV_' . $invoice->invoice_number }}.pdf</a></span>
                    </div>
                </form>

                <div class="row modal-footer">
                    <button type="button" id="btnSendEmailPOST" style="line-height: 30px;"
                        class="btn btn-primary ml-1">{{ __('messages.common.send') }}</button>
                </div>

            </div>
        </div>
    </div>
</div>




<div id="sendWhatsAppModal" class="modal fade address-modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title txtColor">
                    <svg width="25px" height="25px" viewBox="0 0 32 32" fill="none">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M16 31C23.732 31 30 24.732 30 17C30 9.26801 23.732 3 16 3C8.26801 3 2 9.26801 2 17C2 19.5109 2.661 21.8674 3.81847 23.905L2 31L9.31486 29.3038C11.3014 30.3854 13.5789 31 16 31ZM16 28.8462C22.5425 28.8462 27.8462 23.5425 27.8462 17C27.8462 10.4576 22.5425 5.15385 16 5.15385C9.45755 5.15385 4.15385 10.4576 4.15385 17C4.15385 19.5261 4.9445 21.8675 6.29184 23.7902L5.23077 27.7692L9.27993 26.7569C11.1894 28.0746 13.5046 28.8462 16 28.8462Z"
                                fill="#BFC8D0"></path>
                            <path
                                d="M28 16C28 22.6274 22.6274 28 16 28C13.4722 28 11.1269 27.2184 9.19266 25.8837L5.09091 26.9091L6.16576 22.8784C4.80092 20.9307 4 18.5589 4 16C4 9.37258 9.37258 4 16 4C22.6274 4 28 9.37258 28 16Z"
                                fill="url(#paint0_linear_87_7264)"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2C8.26801 2 2 8.26801 2 16C2 18.5109 2.661 20.8674 3.81847 22.905L2 30L9.31486 28.3038C11.3014 29.3854 13.5789 30 16 30ZM16 27.8462C22.5425 27.8462 27.8462 22.5425 27.8462 16C27.8462 9.45755 22.5425 4.15385 16 4.15385C9.45755 4.15385 4.15385 9.45755 4.15385 16C4.15385 18.5261 4.9445 20.8675 6.29184 22.7902L5.23077 26.7692L9.27993 25.7569C11.1894 27.0746 13.5046 27.8462 16 27.8462Z"
                                fill="white"></path>
                            <path
                                d="M12.5 9.49989C12.1672 8.83131 11.6565 8.8905 11.1407 8.8905C10.2188 8.8905 8.78125 9.99478 8.78125 12.05C8.78125 13.7343 9.52345 15.578 12.0244 18.3361C14.438 20.9979 17.6094 22.3748 20.2422 22.3279C22.875 22.2811 23.4167 20.0154 23.4167 19.2503C23.4167 18.9112 23.2062 18.742 23.0613 18.696C22.1641 18.2654 20.5093 17.4631 20.1328 17.3124C19.7563 17.1617 19.5597 17.3656 19.4375 17.4765C19.0961 17.8018 18.4193 18.7608 18.1875 18.9765C17.9558 19.1922 17.6103 19.083 17.4665 19.0015C16.9374 18.7892 15.5029 18.1511 14.3595 17.0426C12.9453 15.6718 12.8623 15.2001 12.5959 14.7803C12.3828 14.4444 12.5392 14.2384 12.6172 14.1483C12.9219 13.7968 13.3426 13.254 13.5313 12.9843C13.7199 12.7145 13.5702 12.305 13.4803 12.05C13.0938 10.953 12.7663 10.0347 12.5 9.49989Z"
                                fill="white"></path>
                            <defs>
                                <linearGradient id="paint0_linear_87_7264" x1="26.5" y1="7"
                                    x2="4" y2="28" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#5BD066"></stop>
                                    <stop offset="1" stop-color="#27B43E"></stop>
                                </linearGradient>
                            </defs>
                        </g>
                    </svg>
                    Send WhatsApp Message
                </h5>
                <button type="button" aria-label="Close" class="close closeBTN" data-dismiss="modal"><i
                        class="fa fa-times-circle" aria-hidden="true"></i></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                <form id="sendWhatsAppForm">

                    <!-- To Field -->
                    <div class="mb-2">
                        <label for="toWhatsApp" class="form-label">From</label>
                        <input type="tel" class="form-control" id="toWhatsApp"
                            placeholder="Recipient's WhatsApp number" value="{{ $settings['phone'] ?? '' }}"
                            required>
                    </div>
                    <label for="toWhatsApp" class="form-label">to</label>
                    {{ Form::select('to[]', $customers, null, ['class' => 'form-control select2', 'id' => 'whatsappto', 'required', 'style' => 'width:100%;', 'multiple' => 'multiple']) }}
                    <!-- Message Field -->
                    <div class="mb-1 mt-1">
                        <label for="whatsAppMessage" class="form-label">Body</label>
                        <textarea class="form-control" id="whatsappBody" style="height: 130px;" placeholder="Type your message here"
                            required>URL : {{ url('/download/invoice/' . $invoice->invoice_number) }}

Invoice Number : {{ $invoice->invoice_number }}
Total Before Discount: {{ number_format($subtotal, 2) }} SAR
Discount {{ isset($invoice->discount_type) ? ($invoice->discount_type == 0 ? '%' : '$') : ' ' }}: {{ number_format($invoice->discount ?? 0, 2) }}
Total After Discount: {{ number_format($totalTaxable, 2) }} SAR
Total VAT: {{ number_format($totalVat, 2) }} SAR
Net Total: {{ number_format($invoice->total_amount, 2) }} SAR
</textarea>
                    </div>

                    <!-- Invoice Row -->
                    {{-- <div class="d-flex align-items-center p-1 "
                        style="background-color: #f8f9fa; border: 1px solid #ddd; height: 30px;">
                        <span class="text-truncate">📄 <a target="_blank">INV_1475.pdf</a></span>
                    </div> --}}
                </form>

                <div class="row modal-footer">
                    {{-- <button type="button" class="btn btnSecondary text-white mr-1" id="btnCancel"
                        data-dismiss="modal">{{ __('messages.common.cancel') }}</button> --}}
                    <button id="btnSendWhatsppPost" type="button" style="line-height: 30px;"
                        class="btn btn-primary ml-1">{{ __('messages.common.send') }}</button>

                </div>
            </div>
        </div>
    </div>
</div>




<div id="sendSMSModal" class="modal fade address-modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title txtColor">Send SMS</h5>
                <button type="button" aria-label="Close" class="close closeBTN" data-dismiss="modal"><i
                        class="fa fa-times-circle" aria-hidden="true"></i></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                <form id="sendSMSForm">
                    <!-- To Field -->
                    <p>Send and SMS Message</p>
                    <div class="mb-2">
                        <label for="toSMS" class="form-label">Send To</label>
                        <input type="text" class="form-control" readonly
                            value="{{ $invoice->customer->company_name ?? '' }}">
                    </div>
                    <div class="mb-2">
                        <label for="toSMS" class="form-label">Phone Number</label>
                        {{ Form::tel('phone', $invoice->customer->phone ?? '', ['class' => 'form-control', 'id' => 'phoneNumber']) }}
                    </div>

                    <!-- Message Field -->
                    <div class="mb-2">
                        <label for="smsMessage" class="form-label">Message</label>
                        <textarea class="form-control" id="smsData" style="height: 130px;" placeholder="Type your message here" readonly
                            required>URL: {{ url('/download/invoice/' . $invoice->invoice_number) }} | Invoice# {{ $invoice->invoice_number }} | Total: {{ number_format($subtotal, 2) }} SAR | Discount: {{ isset($invoice->discount_type) ? ($invoice->discount_type == 0 ? '%' : '$') : '' }} {{ number_format($invoice->discount ?? 0, 2) }} | After Discount: {{ number_format($totalTaxable, 2) }} SAR | VAT: {{ number_format($totalVat, 2) }} SAR | Net: {{ number_format($invoice->total_amount, 2) }} SAR</textarea>
                    </div>
                </form>

                <div class="row modal-footer">
                    {{-- <button type="button" class="btn btnSecondary text-white mr-1" id="btnCancel"
                        data-dismiss="modal">{{ __('messages.common.cancel') }}</button> --}}
                    <a id="btnSendSMSPOST" style="line-height: 30px;"
                        class="btn btn-primary ml-1">{{ __('messages.common.send') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
