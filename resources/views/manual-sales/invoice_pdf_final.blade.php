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

    // Company logo
    $imagePath = $baseImagePath . '/company_logo.png';
    $imageData = file_get_contents($imagePath);
    $base64 = base64_encode($imageData);
    $backgroundImage = 'data:image/png;base64,' . $base64; // Ensure correct format

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
            padding-top: 110px;
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
            height: 110px;
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



        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ $backgroundImage }}');
            background-size: 85%;
            /* Cover to ensure full width */
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.12;
            /* 20% visibility */
            z-index: -1;
            /* Prevent watermark from interfering with content */
        }



        .content {
            margin: 0;
            /* Set body margin to 0 */
            padding: 0;
            /* Set body padding to 0 */
            margin: 0;
            /* Remove left and right margins */
            position: relative;
            z-index: 1;
            /* Bring content above background */

            /* Space for header */

            /* Space for footer */
            box-sizing: border-box;
            /* Include padding in height calculation */
            width: 100%;
            /* Set width to 100% */
        }

        h3 {
            margin: 10px 0;
        }

        .page-break {
            page-break-after: always;
            /* Ensure content starts on a new page */
        }

        .content-title {
            width: 150px;
            border: 2px solid #3b6b67;
            color: #f7996e;
            font-weight: bolder;
            text-align: center;
            margin: auto;
            margin-top: 5px;
            height: 36px;
            line-height: 6px;
            font-size: 20px;

        }

        .sales_info {
            width: 80%;
            border-collapse: collapse;
            font-size: 12pt;

        }

        .sales_info tr {
            padding: 15px;

            /* Adjust the padding as needed */
        }



        .sales_info th,
        .sales_info td {
            text-align: left;
            padding: 3px;
        }

        .sales_table_data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
            /* Set font size */
        }

        .sales_table_data th,
        .sales_table_data td {
            border: 1px solid #848484;
            /* Border for table cells */
            padding: 0;
            /* Remove padding */
            margin: 0;
            /* Remove margin */

        }

        .sales_table_data th {
            /* Light background for header */
        }

        .sales_table_data tr:nth-child(even) {
            /* Zebra striping for even rows */
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <img src="{{ $headerImage }}" style="width: 100%;height:110px; padding: 0px;">
    </header>

    <!-- Footer -->
    <footer>
        <img src="{{ $footerImage }}" style="width: 100%; padding: 0px;">

    </footer>

    <!-- Watermark -->
    <div class="watermark">

    </div>

    <!-- Table Content -->
    <main>


        <div class="content">
            <div class="content-title">
                <p>VAT Invoice</p>
            </div>
            <div style="width:100%;text-align:center; line-height: 5px;font-size:18px;font-weight:400;margin-top:22px">
                TRN:
                {{ $trn_number }}
            </div>
            <div style="width: 100%;margin-top:20px;padding-left:10px;padding-right:10px;">
                <!-- Remove fixed height for responsive content -->
                <table class="sales_info">
                    <tr>
                        <th>Invoice No.</th>
                        <th>Invoice Date</th>
                        <th>Invoice Month</th>
                        <th>VAT Number</th>
                    </tr>

                    <tr>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M y') }}</td>
                        <td>{{ $invoice->customer->vat_number ?? 'N/A' }}</td>
                    </tr>
                    <tr><br></tr>
                    <tr>
                        <th>Customer No.</th>
                        <th colspan="2">Customer Name</th>
                        <th>Payment Mode</th>
                    </tr>
                    <tr>
                        <td>{{ $invoice->customer->code ?? 'N/A' }}</td>
                        <td colspan="2">{{ $invoice->customer->company_name ?? 'N/A' }}</td>
                        <td>
                            @forelse($invoice->paymentModes as $paymentMode)
                                {{ html_entity_decode($paymentMode->name) }}

                            @empty
                                {{ __('messages.common.n/a') }}
                            @endforelse
                        </td>
                    </tr>
                    <tr><br></tr>
                    <tr>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Project Code</th>
                        <th>Project Name</th>
                    </tr>
                    <tr>
                        @if ($invoice->invoiceAddresses && $invoice->invoiceAddresses->isNotEmpty())
                            @foreach ($invoice->invoiceAddresses as $address)
                                <div style="margin-top: 5px;">
                                    {{ html_entity_decode($address->street) .
                                        ', ' .
                                        $address->city .
                                        ', ' .
                                        $address->state .
                                        ', ' .
                                        $address->country .
                                        ', ' .
                                        $address->zip_code }}
                                </div>
                            @endforeach
                        @else
                            <div style="margin-top: 5px;">
                                {{ $invoice->customer['address'] ?? '' }}
                            </div>
                        @endif
                        <td>{{ $invoice->customer->email ?? 'N/A' }}</td>
                        <td>{{ $invoice->project->project_code ?? 'N/A' }}</td>
                        <td>{{ $invoice->project->project_name ?? 'N/A' }}</td>
                    </tr>
                    <tr><br></tr>
                    <tr>
                        <th>Project Location</th>
                        <th>P.O Number</th>
                    </tr>
                    <tr>
                        <td>{{ $invoice->project->project_location ?? 'N/A' }}</td>
                        <td>{{ $invoice->project->po_number ?? 'N/A' }}</td>
                    </tr>
                </table>
                <table class="sales_table_data"
                    style="width: 96.7%; border-collapse: collapse; margin-top: 20px; font-size: 13px;">
                    <thead>
                        <tr style="padding: 0px;">
                            <th style="width: 5%;">S.</th>
                            <th style="width: 7%;">Item</th>
                            <th class="text-center" style="width: 10%;">Category</th>
                            <th class="text-center" style="width: 25%;">Description</th>
                            <th style="width: 2%;">Qty</th>
                            <th class="text-center pr-3" style="width: 8%;">Rate</th>
                            <th style="text-align:center;" class="p-0 pr-1" style="width: 5%;">Disc.</th>
                            <th style="text-align: center;" class="p-0 pr-1" style="width: 8%;">Taxable</th>
                            <th style="text-align: center;" class="p-0  pr-1" style="width: 3%;">Vat %</th>
                            <th style="text-align: center;" class="pr-1" style="width: 3%;">Vat $</th>
                            <th class="p-0 text-center pr-2 w-20" style="width: 7%;">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $vat_amount = 0;
                            // dd($invoice->salesItems->toArray());
                        @endphp
                        @foreach ($invoice->salesItems as $index => $item)
                            @php
                                $vatAmount =
                                    $item->quantity * $item->rate * (1 - $item->discount / 100) * ($item->tax / 100);
                                $includingVat = $item->quantity * $item->rate + $vatAmount;
                            @endphp
                            <tr>
                                <td style="border: 1px solid #848484; text-align:center; width: 25px;">
                                    {{ $index + 1 }}</td>
                                <td
                                    style="border: 1px solid #848484; padding: 2px; text-align:left; padding-left:2%; width:70px;">
                                    {{ $item->item }}</td>
                                {{-- <td
                                style="border: 1px solid #848484; padding: 4px; text-align:left; padding-left:2%;width: 110px; word-wrap: break-word; word-break: break-all;">
                                {{ $item->category->name ?? '' }}</td> --}}
                                <td
                                    style="border: 1px solid #848484; padding: 2px; text-align:left; padding-left:1%;width: 340px; word-wrap: break-word; word-break: break-all;">
                                    {{ html_entity_decode($item->service->title ?? '') }}</td>

                                <td style="border: 1px solid #848484; text-align:center;width:40px;">
                                    {{ $item['quantity'] }}</td>
                                <td style="border: 1px solid #848484; text-align:right; padding-right:5%">
                                    {{ number_format($item['rate'], 2) }}</td>
                                <td style="border: 1px solid #848484; text-align:right; padding-right:5%;width:60px;">
                                    {{ number_format($item['quantity'] * $item['rate'], 2) }}</td>
                                <!-- Excluding VAT Amount -->
                                <td style="border: 1px solid #848484; text-align:right; padding-right:5%">
                                    {{ number_format($item->tax, 2) }}
                                </td>
                                <td style="border: 1px solid #848484; text-align:right; padding-right:5%">

                                    {{ number_format($vatAmount, 2) }} <!-- VAT Amount -->
                                </td>
                                <td style="border: 1px solid #848484; text-align:right; padding-right:5%">
                                    {{ number_format($includingVat, 2) }}
                                    <!-- Including VAT -->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>



                <table
                    style=" width: 97.5%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;">
                    <tr>
                        <!-- Left Section: Two Divs -->
                        <td style="width: 45%; vertical-align: top; padding-right: 20px;">
                            <!-- Top Div -->

                            <div style="width: 100%;height:120px;">
                                <div style="float: left;width:300px;">
                                    <strong> Amount In words</strong><br>
                                    {{ ucfirst($words) }} Only
                                </div>
                                <div style="float: right;">

                                    @php
                                        use Salla\ZATCA\GenerateQrCode;
                                        use Salla\ZATCA\Tags\Seller;
                                        use Salla\ZATCA\Tags\TaxNumber;
                                        use Salla\ZATCA\Tags\InvoiceDate;
                                        use Salla\ZATCA\Tags\InvoiceTotalAmount;
                                        use Salla\ZATCA\Tags\InvoiceTaxAmount;

                                        $qr_seller_trn = $trn_number;
                                        $qr_tax_amount = $totalVat;

                                        $qr_invoice_amount = $newTotal ?? 0;

                                        $qr_code = null;
                                        $qr_seller_name = $com_name;
                                        $qr_invoice_date = !empty($invoice->invoice_date)
                                            ? date('Y-m-d', strtotime($invoice->invoice_date))
                                            : null;

                                        //&& $qr_tax_amount this wass added to below condition .but what if tax amount is zero which is false ,
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
                                        <img style="width: 150px;" src="{{ $qr_code }}" alt="QR Code" />
                                    @endif



                                </div>
                            </div>


                        </td>


                        <!-- Right Section: Calculation Table -->
                        <td style="width: 25%; vertical-align: top;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                <tr>
                                    <td class="font-weight-bold"
                                        style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        <strong>Subtotal</strong>
                                    </td>
                                    <td style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        {{ number_format($subtotal, 2) }}
                                    </td>
                                </tr>


                                <tr>
                                    <th class="font-weight-bold"
                                        style="border: 1px solid #848484; padding: 5px; text-align: right;">Total Discount
                                        {{ isset($invoice->discount_type) ? ($invoice->discount_type == 0 ? '%' : '$') : ' ' }}
                                    </th>
                                    <td class="text-right p-1"
                                        style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        {{ number_format($invoice->discount ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold"
                                        style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        <strong>Total Taxable</strong>
                                    </td>
                                    <td style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        {{ number_format($totalTaxable, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold"
                                        style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        <strong>Total Vat</strong>
                                    </td>
                                    <td style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        {{ number_format($totalVat, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold"
                                        style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        <strong>Net Amount</strong>
                                    </td>
                                    <td style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        {{ number_format($invoice->total_amount ?? 0, 2) }}
                                    </td>
                                </tr>
                            </table>

                        </td>

                    </tr>
                </table>
                <div style="width: 70%;">

                    @if ($invoice->terms && $invoice->terms->isNotEmpty())

                        <div style="font-family: Arial, sans-serif;">
                            <strong style="font-size: 18px;">Terms & Conditions</strong><br>
                            <div class="mt-3">
                                <table style="width: 100%; border-collapse: collapse; margin-top: 0px;">
                                    {{-- <thead>
                                            <tr>
                                                <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">
                                                    SL
                                                </th>
                                                <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">
                                                    Description
                                                </th>
                                            </tr>
                                        </thead> --}}
                                    <tbody>
                                        <!-- Loop through the terms and display them -->
                                        @foreach ($invoice->terms as $index => $estimateTerm)
                                            <tr>
                                                <td style="border: 1px solid #dee2e6; padding: 8px;">
                                                    {{ $index + 1 }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 8px;">
                                                    {{ $estimateTerm['description'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </main>
</body>

</html>
