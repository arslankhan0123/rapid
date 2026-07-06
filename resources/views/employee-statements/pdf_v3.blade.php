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
                Statement Of Employee
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
            <table border="1" cellspacing="0" cellpadding="5" style="width: 99%; border-collapse: collapse;">
                <tr>
                    <td style="width: 2.4cm;">Employee Id</td>
                    <td style="width: 1.22cm;">{{ $employee->iqama_no ?? '' }}</td>
                    <td style="width: 2.30cm;">Department</td>
                    <td style="width: 1.8cm;">{{ $employee->department?->name ?? '' }}</td>
                    <td style="width: 2cm;">Designation</td>
                    <td style="width: 3cm;">{{ $employee->designation?->name ?? '' }}</td>
                    <td style="width: 2.30cm;">Date Of Joining</td>
                    <td style="width:2cm;">{{ \Carbon\Carbon::parse($employee->join_date)->format('d-m-y') ?? '' }}
                    </td>

                </tr>
                <tr>
                    <td>Employee Name </td>
                    <td colspan="3">{{ $employee->name ?? '' }}</td>
                    <td>Email</td>
                    <td>{{ $employee->email ?? '' }}</td>
                    <td>Mobile</td>
                    <td style="width:2cm;">{{ $employee->phone ?? '' }}</td>
                </tr>
                <tr>
                    <td style="width: 2cm;">Home Address</td>
                    <td colspan="3">
                        {{ $employee->city ?? '' }}, {{ $employee->state ?? '' }}, {{ $employee->zip ?? '' }}
                    </td>
                    <td>Start Date</td>
                    <td>{{ !empty($start_date) ? \Carbon\Carbon::parse($start_date)->format('d-m-y') : '' }}</td>
                    <td>End Date</td>
                    <td>{{ !empty($start_date) ? \Carbon\Carbon::parse($end_date)->format('d-m-y') : '' }}</td>

                </tr>

            </table>

        </div>

        <div class="content_items">
            <table class="sales_table_data">
                <thead>
                    <tr style="padding: 0px;">
                        <th style="width: 8%;">Doc Date</th>
                        <th style="width: 6%;">Doc No.</th>
                        <th style="width: 8%;">Doc Type</th>
                        <th style="width: 30%;">Narration</th>
                        <th class="" style="width: 8%;">Debit</th>
                        <th class="" style="width: 5%;">Credit</th>

                        <th class="" style="width: 5%;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalCredit=0;
                        $totalDebit=0;
                        $balance=0;
                    @endphp
                    @foreach ($statement as $index => $item)
                    @php
                        $totalCredit+=$item['credit'];
                        $totalDebit+=$item['debit'];
                        $balance=$totalCredit-$totalDebit;
                    @endphp
                        <tr>
                            <td class="text-center">
                                {{$item['doc_date']}}
                            </td>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>Salary</td>
                            <td>{{$item['type'] }}
                            </td>
                            <td class="text-right">{{number_format($item['debit'],2)}}</td>
                            <td class="text-right">{{number_format($item['credit'],2)}}</td>
                            <td class="text-right">{{number_format($item['balance'],2)}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td> </td>
                        <td class="text-center"></td>
                        <td></td>
                        <td class="text-right"><strong> Total</strong></td>
                        <td class="text-right"><strong>{{ number_format($totalDebit, 2) }}</strong></td>
                        <td class="text-right"><strong>{{ number_format($totalCredit, 2) }}</strong>
                        </td>
                        <td class="text-right"><strong>{{ number_format($balance, 2) }}</strong>
                        </td>
                    </tr>
                </tbody>


            </table>
        </div>

        <table class="signature-table"
            style="position: absolute; bottom: 110; width: 100%; table-layout: fixed; margin: 0 auto; text-align: center; padding-top: 10px;">
            <tr>
                <td style="padding: 10px; text-align: center;">Signature ________________</td>
                <td style="padding: 10px; text-align: center;">Date ________________</td>
                <td style="padding: 10px; text-align: center;">Stamp ________________</td>
            </tr>
        </table>

    </main>

    </script>
</body>

</html>
