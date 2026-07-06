@php
    $contentTitle = 'Tax';
    $footer = 'Footer Content';

    $format = $settings['print_format'] ?? 1;

    $footerheight = '67px';
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
    <title>Quotation PDF</title>
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
            padding-top: 150px;
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
            height: 150px;
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
            background: red;

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
            font-size: 12pt;
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

        .pagenum:before {
            content: "Page " counter(page) " of " counter(pages);
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <img src="{{ $headerImage }}" style="width: 100%;height:150px; padding: 0px;">
        <span class="pagenum"></span>
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
                <p>Quotation</p>
            </div>
            <div style="width:100%;text-align:center; line-height: 5px;font-size:18px;font-weight:400;">
            </div>

            @php
                // dd($estimate->estimateAddresses[0]['country']);
            @endphp
            <div style="width: 100%;margin-top:30px;padding-left:10px;padding-right:10px;">
                <!-- Remove fixed height for responsive content -->
                <table class="sales_info">
                    <tr>
                        <th>Quotation Number </th>
                        <th>Quotation Date</th>
                        <th>Customer Name</th>
                    </tr>
                    <tr>
                        <td>{{ $estimate->estimate_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($estimate->estimate_date)->format('d-m-Y') }}</td>
                        <td colspan="2">{{ $estimate->customer_name ?? 'N/A' }}</td>
                    </tr>
                    <tr><br></tr>
                    <tr>
                        <th>Validity</th>
                        <th>Currency</th>
                        <th>Customer Ref. </th>
                        <th>Address</th>

                    </tr>
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($estimate->estimate_expiry_date)->format('d-m-Y') }}</td>
                        <td>
                            {{ $currency ?? 'N/A' }}
                        </td>
                        <td>{{ $estimate->reference ?? 'N/A' }}
                        </td>

                        <td style="max-width: 100px;">
                            @if (!empty($estimate->estimateAddresses) && isset($estimate->estimateAddresses[0]))
                                @php
                                    $address = $estimate->estimateAddresses[0];
                                @endphp
                                {!! html_entity_decode($address['street']) !!},
                                {!! html_entity_decode($address['city']) !!},
                                {!! html_entity_decode($address['state']) !!},
                                {!! html_entity_decode($address['zip_code']) !!},
                                {!! html_entity_decode($address['country']) !!}
                            @else
                                {{ $estimate->customer['address'] ?? '' }}
                            @endif
                        </td>


                    </tr>
                    <tr><br></tr>
                    <tr>
                        <th>Subject</th>

                    </tr>
                    <tr>

                        <td colspan="4">{{ $estimate->admin_note ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Quality No</th>
                    </tr>
                    <tr>
                        <td colspan="4">{{ $estimate->quality_no ?? '' }}</td>
                    </tr>
                    <tr><br></tr>
                    {{-- <tr>
                    <td colspan="4" style="font-size: 13px; line-height:15px;">
                        <strong>Message </strong> <br> {!! nl2br($estimate->client_note ?? '') !!}
                    </td>
                </tr> --}}
                </table>
                <table class="sales_table_data"
                    style="width: 96.7%; border-collapse: collapse; margin-top: 20px; font-size: 13px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #848484;">SL</th>
                            <th style="border: 1px solid #848484; text-align:center; padding:6px;">Item<br>Code
                            </th>
                            {{-- <th style="border: 1px solid #848484; text-align:center; padding:6px;">Category<br>Name
                        </th> --}}
                            <th style="border: 1px solid #848484;  text-align:center; padding:0px;">Service<br> Name
                            </th>
                            <th style="border: 1px solid #848484;  text-align:center;">Unit<br>Qty</th>
                            <th style="border: 1px solid #848484;  text-align:center; padding:6px;width: 10%">
                                Unit<br>Rate
                            </th>
                            <th style="border: 1px solid #848484; text-align:center; padding:6px;">Excluding <br>VAT
                            </th>
                            <th style="border: 1px solid #848484; width: 8%; text-align:center; padding:6px;">VAT<br>%
                            </th>
                            <th style="border: 1px solid #848484; width: 11%; text-align:center; padding:6px;">
                                VAT<br>Amount
                            </th>
                            <th style="border: 1px solid #848484; width: 12%; text-align:center; padding:6px;">
                                Including<br>VAT</th>
                        </tr>

                    </thead>
                    <tbody>
                        @php
                            $vat_amount = 0;
                            // dd($invoice->salesItems->toArray());
                        @endphp
                        @foreach ($estimate->salesItems as $index => $item)
                            @php
                                $vatAmount =
                                    $item->quantity * $item->rate * (1 - $item->discount / 100) * ($item->tax / 100);
                                $includingVat = $item->quantity * $item->rate + $vatAmount;
                            @endphp
                            <tr>
                                <td style="border: 1px solid #848484; text-align:center; width: 25px;">
                                    {{ $index + 1 }}</td>
                                <td
                                    style="border: 1px solid #848484; padding: 2px; text-align:left; padding-left:2%; width:60px;">
                                    {{ $item->item }}</td>
                                {{-- <td
                                style="border: 1px solid #848484; padding: 4px; text-align:left; padding-left:2%;width: 110px; word-wrap: break-word; word-break: break-all;">
                                {{ $item->category->name ?? '' }}</td> --}}
                                <td
                                    style="border: 1px solid #848484; padding: 2px; text-align:left; padding-left:1%;width: 340px; word-wrap: break-word; word-break: break-all;">
                                    {{ html_entity_decode($item->service->title ?? '') }}</td>

                                <td style="border: 1px solid #848484; text-align:center;width:40px;">
                                    {{ $item['quantity'] }}</td>
                                <td style="border: 1px solid #848484; text-align:right; width:60px;padding-right:5%;">
                                    {{ number_format($item['rate'], 2) }}</td>
                                <td style="border: 1px solid #848484; text-align:right;padding-right:5%;">
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
            font-size: 12pt;">
                    <tr>
                        <!-- Left Section: Two Divs -->
                        <td style="width: 45%; vertical-align: top; padding-right: 20px;">
                            <!-- Top Div -->
                            <div style=" padding: 0px; margin-bottom: 20px;">
                                <strong> Amount In words</strong><br>
                                {{ ucfirst($words) }} Only
                            </div>



                        </td>

                        <!-- Right Section: Calculation Table -->
                        <td style="width: 25%; vertical-align: top;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                <tr>
                                    <td class="font-weight-bold"
                                        style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        <strong>Total Before Discount</strong>
                                    </td>
                                    <td style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        {{ number_format($totalIncludingVAT, 2) }} SAR
                                    </td>
                                </tr>


                                <tr>
                                    <th class="font-weight-bold"
                                        style="border: 1px solid #848484; padding: 5px; text-align: right;">Discount
                                        {{ isset($estimate->discount_type) ? ($estimate->discount_type == 0 ? '%' : '$') : ' ' }}
                                    </th>
                                    <td class="text-right p-1"
                                        style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        {{ number_format($estimate->discount ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold"
                                        style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        <strong>Total After Discount</strong>
                                    </td>
                                    <td style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        {{ number_format($afterDiscount, 2) }} SAR
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold"
                                        style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        <strong>Total VAT</strong>
                                    </td>
                                    <td style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        {{ number_format($totalVATAmount, 2) }} SAR
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold"
                                        style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        <strong>Net Total </strong>
                                    </td>
                                    <td style="border: 1px solid #848484; padding: 5px; text-align: right;">
                                        {{ number_format($newTotal, 2) }} SAR
                                    </td>
                                </tr>
                            </table>

                        </td>

                    </tr>
                </table>

                <div style="width: 70%;">
                    @if ($estimate->terms && $estimate->terms->isNotEmpty())

                        <div style="font-family: Arial, sans-serif;">
                            <strong style="font-size: 18px;">Terms & Conditions</strong><br>
                            <div class="mt-3">
                                <table style="width: 100%; border-collapse: collapse; margin-top: 0px;font-size:13px;">
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
                                        @foreach ($estimate->terms as $index => $estimateTerm)
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
    <script type="text/php">
    if (isset($pdf)) {
        $text = " Page {PAGE_NUM} of {PAGE_COUNT}";
        $font = $fontMetrics->getFont("Arial", "normal");
        $size = 10;
        $color = array(0, 0, 0);
        $word_space = 0.0;  // default
        $char_space = 0.0;  // default
        $angle = 0.0;  // default
        $pdf->page_text(530, 110, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
</script>
</body>

</html>
