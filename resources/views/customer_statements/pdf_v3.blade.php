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
    <title>Statement Of Employee</title>
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
            font-size: 9pt;
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

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>

        <img src="{{ $headerImage }}" style="width: 100%;height:110px; padding: 0px;">
        <div class="content-header">
            <div class="vat">
                Print Date: {{ \Carbon\Carbon::now()->format('d-m-Y') }}
            </div>
            <div class="content_header_title">
                Statement of Customer
            </div>
            <div style="float: right; padding: 3px; margin: 18px 10px 0px 0px; font-size: 13pt;">
                Print Time: {{ \Carbon\Carbon::now()->format('h:i:s A') }}
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
                    <td style="width: 2.4cm;">Customer No</td>
                    <td style="width: 1.22cm;">{{ $customer->code ?? '' }}</td>
                    <td style="width: 2.30cm;">Start Date</td>
                    <td style="width: 1.8cm;">
                        {{ !empty($start_date) ? \Carbon\Carbon::parse($start_date)->format('d-m-y') : '' }}</td>
                    <td style="width: 2cm;">End Date</td>
                    <td style="width: 3cm;">
                        {{ !empty($start_date) ? \Carbon\Carbon::parse($end_date)->format('d-m-y') : '' }}</td>
                    <td style="width: 2.30cm;">Statement No</td>
                    <td style="width:2cm;">{{ $customer->id ?? '' }}</td>

                </tr>
                <tr>
                    <td>Customer Name </td>
                    <td colspan="4">{{ $customer->company_name ?? '' }}</td>
                    <td>Email</td>
                    <td colspan="2">{{ $customer->email ?? '' }}</td>

                </tr>
                <tr>
                    <td style="width: 2cm;"> Address</td>
                    <td colspan="4">
                        {{ $customer->address ?? '' }}
                    </td>
                    <td>Branch</td>
                    <td colspan="2">{{ $branch_name }}</td>
                </tr>

            </table>

        </div>

        {{-- {{ dd($statement) }} --}}
        <div class="content_items">
            <table class="sales_table_data">
                <thead>
                    <tr style="padding: 0px;">
                        <th style="width: 6%;">Invoice No</th>
                        <th style="width: 8%;">Invoice Date</th>
                        <th style="width: 6%;">Type</th>
                        <th style="width: 8%;">Receipt Date </th>
                        <th style="width: 8%;">Month</th>
                        <th style="width: 10%;">Project</th>
                        <th class="" style="width: 8%;">Debit</th>
                        <th class="" style="width: 5%;">Credit</th>
                        <th class="" style="width: 5%;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalDebit = 0;
                        $totalCredit = 0;
                        $totalBalance = 0;
                    @endphp
                    @foreach ($statement as $statement)
                        @php
                            $debit = $statement->debit ?? 0.0;
                            $credit = $statement->credit ?? 0.0;
                            $balance = $statement->balance ?? 0.0;

                            // Accumulate totals
                            $totalDebit += $debit;
                            $totalCredit += $credit;

                        @endphp
                        <tr>
                            <td>{{ $statement->invoice_number ?? '' }}</td>
                            <td>{{ $statement->invoice_date ?? '' }}</td>
                            <td>{{ $statement->type ?? '' }}</td>
                            <td>{{ $statement->receipt_date ?? '' }}</td>
                            <td>{{ $statement->invoice_date ?? '' }}</td>
                            <td>{{ $statement->project_name ?? '' }}</td>
                            <td class="text-right">{{ number_format($debit, 2) }}</td>
                            <td class="text-right">{{ number_format($credit, 2) }}</td>
                            <td class="text-right">{{ number_format($balance, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td> </td>
                        <td class="text-center"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><strong> Total</strong></td>
                        <td class="text-right"><strong>{{ number_format($totalDebit, 2) }}</strong></td>
                        <td class="text-right"><strong>{{ number_format($totalCredit, 2) }}</strong>
                        </td>
                        <td class="text-right"><strong>{{ number_format($totalDebit - $totalCredit, 2) }}</strong>
                        </td>
                    </tr>
                </tbody>


            </table>
        </div>

        <table class="signature-table"
            style="position: absolute; bottom: 120; width: 100%; table-layout: fixed; margin: 0 auto; text-align: center; padding-top: 10px;">
            <tr>
                <td style="padding: 10px; text-align: center;">Signature ________________</td>
                <td style="padding: 10px; text-align: center;">Date ________________</td>
                <td style="padding: 10px; text-align: center;">Stamp ________________</td>
            </tr>
            <tr>
                <td colspan="3"><br>
                    <strong>THANK YOU FOR YOUR BUSINESS!!</strong>
                </td>
            </tr>
        </table>

    </main>

    </script>
</body>

</html>
