@php

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



        }


        .bColor {
            border: .05cm solid #e2e2e2 !important;
        }

        .bgColor {
            background: #fff7f2 !important;
        }

        .page-break {
            page-break-after: always;
            /* Ensure content starts on a new page */
        }




        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .content_header {
            display: table;
            width: 100%;
            background-color: {{ $bgColor }};
            /* Set the background color */
            border: 1px solid {{ $bColor }};
            /* Optional border */
            padding: 10px;
            margin-bottom: 20px;
        }

        .content_header .left,
        .content_header .middle,
        .content_header .right {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .left {
            display: block;
            height: 2.62cm;
        }



        .content_header .left {
            text-align: left;
            width: 20%;
        }

        .content_header .middle {
            text-align: center;

        }

        .content_header .right {
            text-align: right;
            width: 20%;
        }

        .content_items {
            font-size: 12pt;
            width: 100%;
            margin-top: .30cm;
            clear: both;


            /* Ensures the container takes full width */
        }

        .sales_table_data {
            width: 90%;
            /* Makes the table take full width */
            border-collapse: collapse;
            margin: auto;
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

        .text-danger {
            color: red !important;
        }

        .text-success {
            color: green !important;
        }
    </style>
</head>

<body>
    <main>
        <div class="contents">
            <div class="content_header">
                <div class="middle">
                    <h1 style="line-height: 1; color: #f7996e; margin: 0;">{{ $settings['company'] ?? '' }}</h1>
                    <h2 style="line-height: 1; color: #3b6b67; margin: 0;">Profit And Loss</h2>
                    <h3 style="line-height: 1; margin: 0;">
                        {{ \Carbon\Carbon::parse($month ?? '2024-10-10')->format('F Y') }}
                        {{ $branch_name ? ', ' . $branch_name : '' }}</h3>
                    <p style="line-height: 1; margin: 0;">Makkah, New Shubayka<br>Saudi Arabia</p>
                </div>
            </div>

            <div class="content_items">
                <table class="sales_table_data">
                    <thead>
                        <tr style="padding: 0px;">
                            <th style="width: 8%;">Particulars</th>
                            <th style="width: 6%;">Debit</th>
                            <th style="width: 8%;">Credit</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalDebit = 0;
                            $totalCredit = 0;
                        @endphp
                        @foreach ($statement as $index => $item)
                            @php
                                $totalDebit += $item['debit'] ?? 0;
                                $totalCredit += $item['credit'] ?? 0;
                            @endphp
                            <tr>

                                <td class="">{{ $item['type'] }}</td>
                                <td class="text-right">{{ number_format($item['debit'], 2) }}</td>
                                <td class="text-right">{{ number_format($item['credit'], 2) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td> <strong>Net Profit</strong></td>
                            <td class="text-right">
                                <strong class="{{ $totalCredit - $totalDebit < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($totalCredit - $totalDebit, 2) }}
                                </strong>
                            </td>
                            <td class="text-right">
                                <strong></strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right"> <strong>Total</strong></td>
                            <td class="text-right">
                                <strong>{{ number_format($totalDebit, 2) }}</strong>
                            </td>
                            <td class="text-right">
                                <strong>{{ number_format($totalCredit, 2) }}</strong>
                            </td>
                        </tr>
                    </tbody>


                </table>
            </div>
    </main>
</body>

</html>
