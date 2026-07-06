<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Details</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 20px;
        }

        .header img {
            width: 20%;
        }

        .header-content {
            text-align: center;
            width: 65%;
        }

        .table-bordered {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th,
        td {
            border: 1px solid #dee2e6;

            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .amount-summary {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .summary-section {
            width: 30%;
        }

        .thank-you {
            text-align: center;
            margin: 20px 0;
        }

        .address-section {
            margin: 10px 0;
        }

        /* Flex layout for amount summary */
        .summary-container {
            display: flex;
            justify-content: space-between;
        }

        .summary-left {
            width: 45%;
        }

        .summary-center {
            width: 10%;
            text-align: center;
        }

        .summary-right {
            width: 45%;
        }

        .bank-details {
            margin-top: 0px;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <table style="width: 100%; table-layout: fixed;">
        <tr>
            <td style="width: 30%;">
                <img src="data:image/png;base64,{{ $com_logo }}" style="width: 60%;padding:10%;">
            </td>
            <td style="width: 100%;">
                <h4 style="font-weight: bold; text-align:center;">Manara for General Contracting Est.</h4>
                <p style="text-align:center;">
                    C.R. 4031251746 &nbsp; | &nbsp; Mobile: +966 562015468 <br>
                    Email: <a href="mailto:info@manaraest.com">info@manaraest.com</a> &nbsp; | &nbsp;
                    Website: <a href="https://www.manaraest.com" target="_blank">www.manaraest.com</a>
                </p>
                <h2 style="font-weight: bold;text-align:center;color:blue;">TAX INVOICE</h2>
                <p style="text-align:center;color:padding:0px;">TRN: 1000230103000003</p>
            </td>
        </tr>
    </table>
    <div class="header">

    </div>

    <table class="table-bordered tblcustomer" style="width: 100%;">
        <tr>
            <td><strong>Customer Number</strong><br>{{ $invoice->customer->code ?? '' }}</td>
            <td><strong>Customer Name</strong><br>{{ $invoice->customer->company_name ?? '' }}</td>
            <td><strong>Project Code</strong><br>{{ $invoice->project->project_code ?? '' }}</td>
            <td><strong>Project Name</strong><br>{{ $invoice->project->project_name ?? '' }}</td>
        </tr>
        <tr>
            <td><strong>Invoice Number</strong><br>{{ $invoice->id ?? '' }}</td>
            <td><strong>Invoice
                    Date</strong><br>{{ $invoice->posted_at ? \Carbon\Carbon::parse($invoice->posted_at)->format('d-m-Y') : '' }}
            </td>
            <td><strong>VAT Number</strong><br>{{ $invoice->customer->vat_number ?? '' }}</td>
        </tr>
        <tr>
            <td><strong>Address</strong><br>
                @php
                    $addressParts = array_filter([
                        $invoice->customer->customerCountry->name ?? '',
                        $invoice->customer->address->state ?? '',
                        $invoice->customer->address->city ?? '',
                        $invoice->customer->address->zip ?? '',
                        $invoice->customer->address->street ?? '',
                    ]);
                @endphp
                {{ implode(', ', $addressParts) }}
            </td>
            <td><strong>Payment Mode</strong><br>{{ $paymentModes[$invoice->payment_mode] ?? 'N/A' }}</td>
        </tr>
    </table>
 <br>
    <!-- Invoice Table Section -->
    <table class="table-bordered tableEmployee">
        <thead>
            <tr>
                <th>SL</th>
                <th>Dept.</th>
                <th>Total<br>Emp.</th>
                <th>Wroking<br>Hours</th>
                <th>Overtime<br>Hours</th>
                <th>Total<br> Hours</th>
                <th>Rate<br>Hourly</th>
                <th>Excluding <br>Vat</th>
                <th>VAT<br>%</th>
                <th>VAT<br>Amount</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($summeries as $row)
                <tr>
                    <td>{{ $row['sl'] }}</td>
                    <td>{{ $row['department'] }}</td>
                    <td>{{ $row['total_employees'] }}</td>
                    <td>{{ $row['basic_hours'] }}</td>
                    <td>{{ $row['overtimes'] }}</td>
                    <td>{{ $row['total_hours'] }}</td>
                    <td>{{ $row['rate'] }}</td>
                    <th>{{ $row['total'] }}</th>
                    <td>{{ $vat }}%</td>
                    <th>{{ number_format($row['vat'], 2) }}</th>
                    <td>{{ number_format($row['total_with_vat'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <br>
    <table style="width: 100%; table-layout: fixed;">
        <tr>
            <td style="width: 30%; vertical-align: top;">
                <strong>Amount In Words</strong><br>
                {{ $words ?? '' }}
                <div class="bank-details">
                    <strong>Bank Details</strong><br>
                    {!! nl2br(e($bank->value ?? '')) !!}
                </div>
            </td>
            <td style="width: 30%; vertical-align: top;">
                {{-- //qr code starts here --}}
                <div class="row">
                    <div class="col-5">
                        <div class="row">
                            <div class="col-5">
                                @php
                                    use Salla\ZATCA\GenerateQrCode;
                                    use Salla\ZATCA\Tags\Seller;
                                    use Salla\ZATCA\Tags\TaxNumber;
                                    use Salla\ZATCA\Tags\InvoiceDate;
                                    use Salla\ZATCA\Tags\InvoiceTotalAmount;
                                    use Salla\ZATCA\Tags\InvoiceTaxAmount;

                                    $qr_seller_trn = 1000230103000003;
                                    $qr_tax_amount = $totalVat ?? 0;
                                    $qr_invoice_amount = $netAmount ?? 0;

                                    $qr_code = null;
                                    $qr_seller_name = 'Manara for General Contracting Est.';
                                    $qr_invoice_date = $invoice->posted_at ?? '';

                                    //&& $qr_tax_amount this wass added to below condition .but what if tax amount is zero which is false ,

                                    if ($qr_seller_name && $qr_seller_trn && $qr_invoice_date && $qr_invoice_amount) {
                                        $qr_code = GenerateQrCode::fromArray([
                                            new Seller($qr_seller_name),
                                            new TaxNumber($qr_seller_trn),
                                            new InvoiceDate($qr_invoice_date),
                                            new InvoiceTotalAmount(round($qr_invoice_amount)),
                                            new InvoiceTaxAmount($qr_tax_amount),
                                        ])->render();
                                    }
                                @endphp
                                @if ($qr_code)
                                    <img class="mt-1" style="width: 180px;" src="{{ $qr_code }}"
                                        alt="QR Code" />
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
                {{-- q4 code ends here --}}
            </td>
            <td style="width: 30%; vertical-align: top;">
                <table class="table-bordered amount-section" style="width: 100%;">
                    <tr>
                        <td>Total Before Discount</td>
                        <td class="text-right">{{ number_format($subTotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Discount</td>
                        <td class="text-right">{{ number_format($discount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Excluding VAT</td>
                        <td class="text-right">{{ number_format($totalExcludingVat, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total VAT</td>
                        <td class="text-right">{{ number_format($totalVat, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Including VAT</td>
                        <td class="text-right">{{ number_format($netAmount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>




    <!-- Bank Details Section -->


    <div class="thank-you">
        <p>THANK YOU FOR YOUR BUSINESS!!</p>
        <p>Makkah Al Mukarramah . Omaima Bint Al Khattab Street . Tel: 012538109 . P.O.Box 3177 Code 24241 . Kingdom of
            Saudi Arabia</p>
    </div>
</body>

</html>
