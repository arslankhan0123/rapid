@php

    $contentTitle = 'Tax';
    $footer = 'Footer Content';

    $format = $settings['print_format'] ?? 1;

    $footerheight = '52px';
    if ($format == 2) {
        $footerheight = '88px';
    }
    // Define the base path for different formats
    $baseImagePath = public_path('print/format_' . $format);

    // Company header image
    $headerPath = $baseImagePath . '/header.jpg';
    $imageheader = file_get_contents($headerPath);
    $headerImage = base64_encode($imageheader);
    $headerImage = 'data:image/jpg;base64,' . $headerImage;

    // Company footer image
    $footerPath = $baseImagePath . '/footer.jpg';
    $imagefooter = file_get_contents($footerPath);
    $imagefooterPath = base64_encode($imagefooter);
    $footerImage = 'data:image/jpg;base64,' . $imagefooterPath;

    if ($settings['print_format'] == 1) {
        //smit

        $com_name = 'شركة سهم المنار للتجارة والمقاولات';
        $trn_number = 311204277500003;
    } else {
        //ran

        $com_name = 'مختبر راية  النجاح لمواد البناء';
        $trn_number = 310429743800003;
    }
    $bgColor = '#fff7f2';
    $bColor = '#e2e2e2';

@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        @page {
            margin-top: 0px;
            /* Adjust based on header height */
            margin-bottom: 0px;
            /* Adjust based on footer height */
            margin-left: 0px;
            margin-right: 00px;
        }

        body {
            padding-left: .10cm;
            padding-right: .10cm;
            padding-top: 172px;
            /* Same as header height */
            padding-bottom: {{ $footerheight }};
            /* Same as footer height */

        }

        /* Set up header and footer */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;

            /* Adjust the height of the header image */
            text-align: center;
            z-index: 10;
            /* Ensure the header stays above content */
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: {{ $footerheight }};
            /* Adjust the height of the footer image */
            text-align: center;
            z-index: 10;


            /* Ensure the footer stays above content */
        }



        .bColor {
            border: .05cm solid #e2e2e2 !important;
        }

        .bgColor {
            background: #fff7f2 !important;
        }

        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            background-size: 85%;
            /* Cover to ensure full width */
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.12;
            /* 20% visibility */
            z-index: -1;
            /* Prevent watermark from interfering with content */
        }

        .page-break {
            page-break-after: always;
            /* Ensure content starts on a new page */
        }

        .content-header {
            width: 100%;
            height: 1.80cm;
            margin: 0;

        }

        /* VAT section */
        .vat {
            display: inline-block;
            padding: 5px;
            font-size: 13pt;
            padding-left: .26cm;
            text-align: start;
            padding-top: 20px;
            width: 30%;
            float: left;
            text-align: left;
            /* Adjust width as needed */
        }

        /* Quotation section with blue background */
        .content_header_title {
            vertical-align: middle;
            margin-top: 10px;
            float: left;
            display: inline-block;
            width: 8cm;
            height: 1.13cm;
            text-align: center;
            font-size: 20pt;
            line-height: 40px;
            border: 1px solid {{ $bColor }};
            background: {{ $bgColor }};

        }

        .content_info {

            font-size: 10pt;
        }

        .content_info table {
            border: 1px solid {{ $bColor }};
            /* Set border color */
            border-collapse: collapse;
            width: 100%;

        }

        .content_info table th,
        .content_info table td {
            border: 1px solid {{ $bColor }};
            /* Set border color for cells */
            padding: 5px;
            /* Adjust padding for cells */
            border: 1px solid {{ $bColor }};
        }

        .content_info table td:nth-child(odd) {
            background: {{ $bgColor }};
            /* Background color for odd rows' first cell */
        }

        .content_items {
            font-size: 8pt;
            width: 100%;
            margin-top: .30cm;
            clear: both;


            /* Ensures the container takes full width */
        }

        .sales_table_data {
            width: 100%;
            /* Makes the table take full width */
            border-collapse: collapse;

            border: 1px solid {{ $bColor }};
            /* Ensures borders collapse for cleaner layout */
        }

        .sales_table_data th {
            background: {{ $bgColor }};
            /* Set the background color for table headers */
            padding: 8px;
            /* Add padding to table header cells */
            text-align: center;
            border: 1px solid {{ $bColor }};
        }

        .sales_table_data td {
            padding: 8px;
            border: 1px solid {{ $bColor }};
            /* Add padding to table data cells */
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>

        <img src="{{ $headerImage }}" style="width: 100%;height:110px; padding: 0px;">
        <div class="content-header">
            <div class="vat">
                Vat No. : 310429743800003
            </div>
            <div class="content_header_title">
                Return Invoice
            </div>
        </div>
    </header>

    <!-- Footer -->
    <footer>
        <img src="{{ $footerImage }}" style="width: 100%; padding: 0px;">

    </footer>


    <!-- Table Content -->
    <main>
        <div class="content_info">
            <table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 2cm;">Invoice No.</td>
                    <td style="width: 1.22cm;">{{ $creditNote->invoice?->invoice_number }}</td>
                    <td style="width: 2.35cm;">Invoice Date</td>
                    <td style="width: 2cm;">
                        {{ \Carbon\Carbon::parse($creditNote->invoice?->invoice_date)->format('d-m-Y') }}
                    </td>
                    <td style="width: 2.48cm;">Invoice Month</td>
                    <td style="width: 1.5cm;">
                        {{ \Carbon\Carbon::parse($creditNote->invoice?->invoice_date)->format('M, y') }}</td>
                    <td style="width: 2.40cm;">Cust. Vat No.</td>
                    <td style="width:2cm;">{{ $creditNote->customer->vat_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="width: 2cm;">Cust. No. </td>
                    <td style="width: 1.22cm;">{{ $creditNote->customer->code ?? 'N/A' }}</td>
                    <td style="width: 2cm;">Cust. Name</td>
                    <td colspan="3">{{ $creditNote->customer->company_name ?? 'N/A' }}</td>

                    <td style="width: 2.40cm;">Payment Terms</td>
                    <td style="width:2cm;">Credit</td>
                </tr>
                <tr>
                    <td>Project Code </td>
                    <td>{{ $creditNote->invoice?->project?->project_code ?? 'N/A' }}</td>
                    <td>Project Name</td>
                    <td>{{ $creditNote->invoice?->project->project_name ?? 'N/A' }}</td>
                    <td>Project Location</td>
                    <td>{{ $creditNote->invoice?->project?->project_location ?? 'N/A' }}</td>
                    <td style="">Email</td>
                    <td style="width:2cm; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                        {{ $creditNote->invoice->customer->email ?? 'N/A' }}</td>

                </tr>
                <tr>
                    <td>Return No </td>
                    <td>{{ $creditNote->credit_note_number ?? 'N/A' }}</td>
                    <td>Return Date</td>
                    <td>{{ \Carbon\Carbon::parse($creditNote->credit_note_date)->format('d-m-Y') }}</td>
                    <td>Vendor Code</td>
                    <td>{{ $creditNote->vendor_code ?? 'N/A' }}</td>
                    <td>Address</td>
                    <td>
                        @if ($creditNote->creditNoteAddresses && $creditNote->creditNoteAddresses->isNotEmpty())
                            @foreach ($creditNote->creditNoteAddresses as $address)
                                <div style="width: 2cm; white-space: nowrap; overflow-wrap: normal;">
                                    {{ implode(', ', array_filter([$address->street, $address->city, $address->state, $address->country, $address->zip_code])) }}
                                </div>
                            @endforeach
                        @else
                            <div
                                style="width:2cm; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                                {{ $creditNote->customer['address'] ?? 'N/A' }}
                            </div>

                        @endif


                    </td>
                </tr>
                <tr>
                    <td>Remarks</td>
                    <td colspan="3">{{ $creditNote->admin_text ?? 'N/A' }}</td>
                    <td>Branch</td>
                    <td colspan="3">{{ $creditNote->branch?->name ?? '' }}</td>
                </tr>
                {{-- <tr>
                    <td style="width: 2cm;">Due Days</td>
                    <td style="width: 1.22cm;">
                        {{ \Carbon\Carbon::parse($creditNote->due_date)->diffInDays(\Carbon\Carbon::parse($creditNote->invoice_date)) }}
                    </td>
                    <td style="width: 2cm;">Due Date</td>
                    <td style="width: 2cm;">{{ \Carbon\Carbon::parse($creditNote->due_date)->format('d-m-Y') }}</td>
                    <td style="width: 2.48cm;">P.O Number</td>
                    <td style="width: 3.36cm;">{{ $creditNote->project->po_number ?? 'N/A' }}</td>
                    <td style="width: 2.40cm;">Project Name</td>
                    <td style="width:2cm;">Credit</td>
                     <td >Address</td>
                    <td style="width: 2cm;">
                        @if ($creditNote->invoiceAddresses && $creditNote->invoiceAddresses->isNotEmpty())
                            @foreach ($creditNote->invoiceAddresses as $address)
                                <div
                                    style="width:2cm; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                                    {{ implode(', ', array_filter([$address->street, $address->city, $address->state, $address->country, $address->zip_code])) }}
                                </div>
                            @endforeach
                        @else
                            <div
                                style="width:2cm; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                                {{ $creditNote->customer['address'] ?? 'No address available' }}
                            </div>

                        @endif


                    </td>
                </tr> --}}

            </table>

        </div>
        <div class="content_items">
            <table class="sales_table_data">
                <thead>
                    <tr style="padding: 0px;">
                        <th style="width: 5%;">S.</th>
                        <th style="width: 7%;">Item</th>
                        <th class="text-center" style="width: 25%;">Description</th>
                        <th style="width: 2%;">Qty</th>
                        <th class="text-center pr-3" style="width: 8%;">Rate</th>
                        <th style="text-align:center;" class="p-0 pr-1" style="width: 5%;">Disc.</th>
                        <th style="text-align: center;" class="p-0 pr-1" style="width: 8%;">Taxable</th>
                        <th style="text-align: center;" class="p-0 pr-1" style="width: 3%;">Vat %</th>
                        <th style="text-align: center;" class="pr-1" style="width: 3%;">Vat </th>
                        <th class="p-0 text-center pr-2 w-20" style="width: 7%;">Amount</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($creditNote->salesItems as $index => $item)
                        @php
                            $befoPrice = $item->quantity * $item->rate - $item->discount; // After discount
                            $vatAmount = $befoPrice * ($item->tax / 100); // Apply tax on the net price
                            $netPrice = $befoPrice + $vatAmount;

                        @endphp
                        <tr>
                            <td style=" text-align:center; width: .70cm;">
                                {{ $index + 1 }}</td>
                            <td style=" padding: 2px; text-align:left; padding-left:5px; width:1.10cm;">
                                {{ $item->item }}</td>
                            <td
                                style=" padding: 2px; text-align:left; padding-left:1%;width:6.49cm; word-wrap: break-word; word-break: break-all;">
                                {{ html_entity_decode($item->service->title ?? '') }}</td>

                            <td style=" text-align:center;width:.96cm;">
                                {{ $item['quantity'] }}</td>
                            <td style="text-align:right; width:1.50cm;">
                                {{ number_format($item['rate'], 2) }}</td>
                            <td style=" text-align:right;width:1.30cm;">
                                {{ number_format($item['discount'], 2) }}</td>
                            <td style=" text-align:right;width:1.30cm;">
                                {{ number_format($item['quantity'] * $item['rate'] - $item['discount'], 2) }}</td>
                            <!-- Excluding VAT Amount -->
                            <td style=" text-align:right; width:1.50cm;">
                                {{ number_format($item->tax, 2) }}%
                            </td>
                            <td style=" text-align:right; padding-right:5%">
                                {{ number_format($vatAmount, 2) }} <!-- VAT Amount -->
                            </td>
                            <td style=" text-align:right; padding-right:5%">
                                {{ number_format($netPrice, 2) }}
                                <!-- Including VAT -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <div class="content_calculation" style="margin-top:10px;font-size:10pt;">
            <table cellspacing="0" cellpadding="5" style="width: 11cm; border-collapse: collapse;float:left;">

                <tr>
                    <td
                        style="width: 9.5cm;  background: {{ $bgColor }};   border: 1px solid {{ $bColor }};">
                        <strong>Amount In words</strong>
                    </td>
                    <td>
                </tr>
                <tr>
                    <td style=" border: 1px solid {{ $bColor }};">
                        {{ ucfirst($words) }} Only
                    </td>
                    <td>
                </tr>
                <tr>
                    <td
                        style="width: 9.5cm;  background: {{ $bgColor }};   border: 1px solid {{ $bColor }};">
                        <strong>Bank Details</strong>
                    </td>
                    <td>
                </tr>
                <tr>
                    <td style="width: 9.5cm;   border: 1px solid {{ $bColor }};height:2cm;padding:0px;">

                        <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
                            <tr>
                                <!-- Left side: 60% width -->
                                <td style="width: 60%; border-right: 1px solid {{ $bColor }};">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <!-- Row 1 -->
                                            <td
                                                style="width: 40%; padding: 4px; border-bottom: 1px solid {{ $bColor }};border-right:1px solid {{ $bColor }};">
                                                Bank Name</td>
                                            <td
                                                style="width: 60%; padding: 4px; border-bottom: 1px solid {{ $bColor }};">
                                                {{ $bank->name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <!-- Row 2 -->
                                            <td
                                                style="width: 40%; padding: 4px; border-bottom: 1px solid {{ $bColor }};border-right:1px solid {{ $bColor }};">
                                                A/C No.</td>
                                            <td
                                                style="width: 60%; padding: 4px; border-bottom: 1px solid {{ $bColor }};">
                                                Details for Row 2</td>
                                        </tr>
                                        <tr>
                                            <!-- Row 2 -->
                                            <td
                                                style="width: 40%; padding: 4px; border-bottom: 1px solid {{ $bColor }};border-right:1px solid {{ $bColor }};">
                                                IBAN</td>
                                            <td
                                                style="width: 60%; padding: 4px; border-bottom: 1px solid {{ $bColor }};">
                                            </td>
                                        </tr>
                                        <tr>
                                            <!-- Row 2 -->
                                            <td
                                                style="width: 40%; padding: 4px; border-right:1px solid {{ $bColor }};">
                                                Address</td>
                                            <td style="width: 60%; padding: 4px;">

                                            </td>
                                        </tr>

                                    </table>
                                </td>

                                <!-- Middle column: 1px wide (no border in the middle here) -->
                                <td style="width: 1px;"></td>

                                <!-- Right side: 40% width -->
                                <td style="width: 40%; ">
                                    @php
                                        use Salla\ZATCA\GenerateQrCode;
                                        use Salla\ZATCA\Tags\Seller;
                                        use Salla\ZATCA\Tags\TaxNumber;
                                        use Salla\ZATCA\Tags\InvoiceDate;
                                        use Salla\ZATCA\Tags\InvoiceTotalAmount;
                                        use Salla\ZATCA\Tags\InvoiceTaxAmount;

                                        $qr_seller_trn = $trn_number;
                                        $qr_tax_amount = $totalVat;

                                        $qr_invoice_amount = $creditNote->total_amount ?? 0;

                                        $qr_code = null;
                                        $qr_seller_name = $com_name;

                                        $qr_invoice_date = !empty($creditNote->credit_note_date)
                                            ? date('Y-m-d', strtotime($creditNote->credit_note_date))
                                            : null;

                                        if (
                                            $qr_seller_name &&
                                            $qr_seller_trn &&
                                            $qr_invoice_date &&
                                            $qr_invoice_amount
                                        ) {
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
                                        <img style="width: 3.6cm;height:2.2cm;" src="{{ $qr_code }}"
                                            alt="QR Code" />
                                    @endif
                                </td>
                            </tr>
                        </table>



                        <div style="width: 30%;float:right;">

                        </div>
                    </td>

                    <td>

                </tr>

            </table>


            <table style="width: 8cm; border-collapse: collapse; font-size: 13px;float: right;">
                <tr>
                    <td class="font-weight-bold bgColor"
                        style="border: 1px solid {{ $bColor }}; padding: 6px; text-align: right;">
                        <strong>Subtotal</strong>
                    </td>
                    <td style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                        {{ number_format($subtotal, 2) }}
                    </td>
                </tr>


                <tr>
                    <th class="font-weight-bold bgColor"
                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                        Total Discount

                        {{-- {{ isset($creditNote->discount_type) ? ($creditNote->discount_type == 0 ? '%' : '$') : ' ' }} --}}
                    </th>
                    <td class="text-right p-1"
                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                        {{ number_format($creditNote->discount ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold bgColor"
                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                        <strong>Total Taxable</strong>
                    </td>
                    <td style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                        {{ number_format($totalTaxable, 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="font-weight-bold bgColor"
                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                        <strong>Total Vat</strong>
                    </td>
                    <td style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                        {{ number_format($totalVat, 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="font-weight-bold bgColor"
                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                        <strong>Round Off</strong>
                    </td>
                    <td style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                        {{ number_format($creditNote->adjustment, 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="font-weight-bold bgColor"
                        style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                        <strong>Net Amount</strong>
                    </td>
                    <td style="border: 1px solid {{ $bColor }}; padding: 7px; text-align: right;">
                        {{ number_format($creditNote->total_amount ?? 0, 2) }}
                    </td>
                </tr>
            </table>

        </div>
        @if ($creditNote->terms && $creditNote->terms->isNotEmpty())
            <div style="font-family: Arial, sans-serif; clear: both;font-size:10pt;">

                <div class="mt-3">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                        <thead>
                            <tr>
                                <th style=" padding: 8px; text-align: left; background-color: {{ $bgColor }};">
                                    Terms and Conditions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop through the terms and display them as bullet points -->
                            @foreach ($creditNote->terms as $estimateTerm)
                                <tr>
                                    <td style="padding: 8px;">
                                        <ul style="margin: 0; padding-left: 15px;">
                                            <li>{{ $estimateTerm['description'] }}</li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        <table style="width: 100%; text-align: center;margin-top:30px;">
            <tr>
                <td style="width: 33.33%;">Signature __________________</td>
                <td style="width: 33.33%;">Date __________________</td>
                <td style="width: 33.33%;">Stamp __________________</td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 10px;"><br>
                    <strong>THANK YOU FOR YOUR BUSINESS!!</strong>
                </td>
            </tr>
        </table>


    </main>
    <script type="text/php">
    if (isset($pdf)) {
        $text = " Page {PAGE_NUM} of {PAGE_COUNT}";
        $font = $fontMetrics->getFont("times", "normal");
        $size = 13;
        $color = array(0, 0, 0);
        $word_space = 0.0;  // default
        $char_space = 0.0;  // default
        $angle = 0.0;  // default
        $pdf->page_text(10, 780, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
</script>
</body>

</html>
