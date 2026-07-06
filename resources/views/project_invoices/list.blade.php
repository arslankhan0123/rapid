@extends('layouts.app')
@section('title')
    {{ __('messages.project-invoices.details') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <!-- Additional CSS Styling -->
    <style>
        .invoice-details-section table {

            background-color #f8f9fa;
            margin-top 20px;
            font-size 14px;
        }

        .invoice-details-section table td {
            padding 10px;
        }

        .table-bordered {}

        .table-bordered th,
        .table-bordered td {}

        .table-striped tbody trnth-of-type(odd) {
            background-color #f2f2f2;
        }

        .table th,
        .table td {
            vertical-align middle;
        }

        .table-primary {

            color white;
        }

        .form-control {
            font-size 14px;
            padding 5px;
        }

        .table-responsive {
            margin-top 30px;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.project-invoices.details') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    <div class="float-right">
                        <a href="{{ route('project-invoices.index') }}"
                            class="btn btn-primary form-btn">{{ __('messages.department.list') }} </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @if (session()->has('flash_notification'))
                        @foreach (session('flash_notification') as $message)
                            <div class="alert alert-{{ $message['level'] }}">
                                {{ $message['message'] }}
                            </div>
                        @endforeach
                    @endif

                    <div class="row justify-content-end mb-1">
                        @can('export_project_invoices')
                            <a href="{{ route('project-invoices.export-details', ['invoice' => $invoice->id]) }}"
                                class="btn btn-primary m-1">
                                Export
                            </a>
                        @endcan

                        @can('export_project_invoices')
                            <button type="button" id="btnSave" class="btn btn-success m-1">
                                Paid
                            </button>
                        @endcan
                    </div>

                    <!-- Customer and Project Details Section -->
                    <table class="table">
                        <tr>
                            <td>
                                <strong>Customer Number</strong><br>
                                <span>{{ $invoice->customer->code ?? '' }}</span>
                            </td>
                            <td>
                                <strong>Customer Name</strong><br>
                                <span>{{ $invoice->customer->company_name ?? '' }}</span>
                            </td>
                            <td>
                                <strong>Invoice Number</strong><br>
                                <span>{{ $invoice->id }}</span>
                            </td>
                            <td>
                                <strong>Invoice Date</strong><br>
                                <span>{{ $invoice->posted_at ? \Carbon\Carbon::parse($invoice->posted_at)->format('d-m-Y') : '' }}</span>
                            </td>

                        </tr>
                        <tr>
                            <td>
                                <strong>Email Address</strong><br>
                                <span>{{ $invoice->customer->email ?? '' }}</span>
                            </td>
                            <td>
                                <strong>Address</strong><br>
                                @php
                                    $addressParts = array_filter([
                                        $invoice->customer->customerCountry->name ?? '',
                                        $invoice->customer->address->state ?? '',
                                        $invoice->customer->address->city ?? '',
                                        $invoice->customer->address->zip ?? '',
                                        $invoice->customer->address->street ?? '',
                                    ]);
                                @endphp
                                <span>{{ implode(', ', $addressParts) }}</span>
                            </td>
                            <td>
                                <strong>Payment Mode</strong><br>
                                {{ Form::select('payment_mode', $paymentModes, $invoice->payment_mode ?? '', ['class' => 'form-control', 'id' => 'paymentMode', 'required']) }}
                            </td>
                            <td>
                                <strong>VAT Number</strong><br>
                                <span>{{ $invoice->customer->vat_number ?? '' }}</span>
                            </td>
                        </tr>
                        <tr>

                            <td>
                                <strong>Project Code</strong><br>
                                <span>{{ $invoice->project->project_code ?? '' }}</span>
                            </td>
                            <td>
                                <strong>Project Name</strong><br>
                                <span>{{ $invoice->project->project_name ?? '' }}</span>
                            </td>
                            <td>
                                <strong>Project Location</strong><br>
                                <span>{{ $invoice->project->project_name ?? '' }}</span>
                            </td>
                        </tr>
                        {{-- <tr>
                            <td>
                                <strong>Currency</strong><br>
                                @php
                                    // Assuming $currencies is an indexed array starting from 0
                                    $currencyId = $invoice->customer->currency ?? null;
                                    $currencyName =
                                        $currencyId !== null && isset($currencies[$currencyId])
                                            ? $currencies[$currencyId]
                                            : '';
                                @endphp
                                <span>{{ $currencyName }}</span><br>
                            </td>



                        </tr> --}}
                    </table>


                    <!-- Invoice Table Section -->
                    <div class="table-responsive mb-1 ">
                        <table class="table table-striped table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Department</th>
                                    <th>Total Employees</th>
                                    <th>Wroking Hours</th>
                                    <th>Overtime Hours</th>
                                    <th>Total Hours</th>
                                    <th>Rate Hourly</th>
                                    <th>Excluding Vat</th>
                                    <th>VAT Rate </th>
                                    <th>VAT Amount</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($summeries as $row): ?>
                                <tr>
                                    <td><?= $row['sl'] ?></td>
                                    <td><?= $row['department'] ?></td>
                                    <td><?= $row['total_employees'] ?></td>
                                    <td><?= $row['basic_hours'] ?></td>
                                    <td><?= $row['overtimes'] ?></td>
                                    <td><?= $row['total_hours'] ?></td>
                                    <td><?= $row['rate'] ?></td>
                                    <td><?= $row['total'] ?></td>
                                    <td>{{ $vat }}%</td>
                                    <td><?= number_format($row['vat'], 2) ?></td>
                                    <td><?= number_format($row['total_with_vat'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>




                    </div>

                    <div class="row ml-2">
                        <div class="col-md-7 justify-content-start">
                            <br>
                            <div class="row">
                                <div>
                                    <h5>Amount In words</h5>
                                    <p id="total_amount_words"></p>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <h5>Bank Details</h5>

                            </div>
                            <div class="row">
                                <p>{!! nl2br(e($bank->value ?? '')) !!}</p>
                            </div>
                        </div>
                        <div class="col-md-5 ">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>Total Before Discount</td>
                                        <td class="text-right" style="padding-right:0px;padding-top:5px;">

                                            <input type="text" id="total_amount" class="form-control"
                                                value="{{ array_sum(array_column($summeries, 'total')) }}" readonly
                                                style="text-align: end;">

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total Discount:</td>
                                        <td class="text-right" style="padding-right:0px;">
                                            <input type="number" id="discount" value="{{ $invoice->discount }}"
                                                min="0" class="form-control" style="text-align: end;">
                                        </td>
                                    </tr>
                                    <tr>

                                        <td>Total Exluding Vat</td>
                                        <td class="text-right" style="padding-right:0px;">
                                            <input type="text" id="excludingVat"
                                                value="{{ array_sum(array_column($summeries, 'total')) }}" readonly
                                                class="form-control" style="text-align: end;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total VAT Amount</td>
                                        <td class="text-right" style="padding-right:0px;">
                                            <input type="text" id="total_vat"
                                                value="{{ array_sum(array_column($summeries, 'vat')) }}" readonly
                                                class="form-control" style="text-align: end;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total Including VAT </td>
                                        <td class="text-right" style="padding-right:0px;">
                                            <input type="text" id="net_amount"
                                                value="{{ array_sum(array_column($summeries, 'total_with_vat')) }}"
                                                readonly class="form-control" style="text-align: end;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Partially</td>
                                        <td class="text-right" style="padding-right:0px;">
                                            <input type="number" id="total_paid" value="{{ $invoice->paid_amount }}"
                                                min="0" class="form-control " style="text-align: end;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Due</td>
                                        <td class="text-right" style="padding-right:0px;">
                                            <input type="text" id="due" value="0" readonly
                                                class="form-control" style="text-align: end;">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </div>


            <input type="hidden" id="invoice_id" value="{{ $invoice->id }}">
            <input type="hidden" id="vat" value="{{ $vat }}">
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
        $(document).ready(function() {

            function calculateAmounts() {
                // Get values from the relevant input fields
                var totalAmount = parseFloat($("#total_amount").val()) || 0; // Total before discount
                var discount = parseFloat($("#discount").val()) || 0; // Discount amount
                var totalVat = parseFloat($("#total_vat").val()) || 0; // Total VAT amount
                var totalPaid = parseFloat($("#total_paid").val()) || 0; // Amount paid

                // Ensure discount doesn't exceed total amount
                discount = Math.min(discount, totalAmount);

                // Calculate total excluding VAT
                var totalExcludingVat = totalAmount - discount; // Subtract discount from total amount
                $("#excludingVat").val(totalExcludingVat.toFixed(2)); // Update total excluding VAT display

                // Calculate total including VAT
                var totalIncludingVAT = totalExcludingVat + totalVat;
                $("#net_amount").val(totalIncludingVAT.toFixed(2)); // Update total including VAT display

                // Calculate due amount
                var due = totalIncludingVAT - totalPaid; // Calculate due amount
                $("#due").val(due.toFixed(2)); // Update due amount display

                console.log(totalIncludingVAT);
                $("#total_amount_words").html(capitalizeFirstLetter(numberToWords(Math.round(totalIncludingVAT))));


            }

            // Attach input event listener to the discount and total paid fields
            $("#discount, #total_paid").on("input", function() {
                calculateAmounts(); // Recalculate amounts on input
            });

            // Initial calculation
            calculateAmounts(); // Calculate amounts when the page is loaded
        });
    </script>


    <script>
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        // Example usage:
        function numberToWords(num) {
            const ones = [
                '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
                'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen',
                'nineteen'
            ];
            const tens = [
                '', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
            ];
            const thousands = ['', 'thousand', 'million', 'billion'];

            if (num === 0) return 'zero';

            let words = ''; // Use let instead of const
            let counter = 0;

            while (num > 0) {
                let n = num % 1000; // Use let instead of const
                if (n !== 0) {
                    let str = ''; // Use let instead of const
                    if (n >= 100) {
                        str += ones[Math.floor(n / 100)] + ' hundred ';
                        n %= 100;
                    }
                    if (n >= 20) {
                        str += tens[Math.floor(n / 10)] + ' ';
                        n %= 10;
                    }
                    if (n > 0) {
                        str += ones[n] + ' ';
                    }
                    words = str + thousands[counter] + ' ' + words;
                }
                num = Math.floor(num / 1000);
                counter++;
            }

            return words.trim();
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#btnSave').click(function() {
                processingBtn("#btnSave", 1);
                // Get the values of discount and advance
                const discount = $('#discount').val();
                const advance = $('#total_paid').val();
                const invoiceId = $('#invoice_id').val(); // Get the invoice ID
                const paymentMode = $("#paymentMode").val();
                const vat = $("#vat").val();
                // Prepare the data to send
                const data = {
                    discount: discount,
                    paid_amount: advance,
                    vat: vat,
                    payment_mode: paymentMode
                    // Add any other data you might need to send
                };
                $.ajax({
                    url: route('project-invoices.update', invoiceId),
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                    type: 'POST', // Specify the POST method
                    success: function(response) {
                        processingBtn("#btnSave", 0);
                        displaySuccessMessage(response.message);
                        const url = route('project-invoices.index', );
                        window.location.href = url;
                    },
                    error: function(result) {
                        processingBtn("#btnSave", 0);
                        displayErrorMessage(result.responseJSON.message);
                    }
                });
            });
        });
    </script>
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
                $button.html('Paid'); // Reset button text
            }
        }
    </script>
@endsection
