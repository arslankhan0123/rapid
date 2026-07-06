<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sales Summary Reports</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            direction: ltr;
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        th,
        td {
            padding: 4px;
            text-align: left;
            word-wrap: break-word;
            overflow: hidden;
            border: 1px solid #ddd;
            font-size: 9px;
        }

        th {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        tfoot td {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .arabic {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            unicode-bidi: embed;
            text-align: right;
        }

        /* Set specific widths for columns */
        th:nth-child(1),
        td:nth-child(1) {
            width: 4%;
        }

        th:nth-child(2),
        td:nth-child(2) {
            width: 12%;
        }

        th:nth-child(3),
        td:nth-child(3) {
            width: 28%;
        }

        th:nth-child(4),
        td:nth-child(4) {
            width: 12%;
        }

        th:nth-child(5),
        td:nth-child(5) {
            width: 11%;
        }

        th:nth-child(6),
        td:nth-child(6) {
            width: 11%;
        }

        th:nth-child(7),
        td:nth-child(7) {
            width: 11%;
        }

        th:nth-child(8),
        td:nth-child(8) {
            width: 11%;
        }
    </style>

</head>

<body>
    <h3>{{ config('app.name') }} - Sales Report</h3>

    <table>
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>Invoice Number</th>
                <th>Customer Name</th>
                <th>Invoice Date</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Taxable Amount</th>
                <th class="text-right">Tax Amount</th>
                <th class="text-right">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDiscount = 0;
                $totalTaxable = 0;
                $totalTax = 0;
                $totalAmount = 0;
            @endphp

            @foreach ($exportData as $row)
                <tr>
                    <td class="text-center">{{ $row['#'] }}</td>
                    <td>{{ $row['Invoice Number'] }}</td>
                    <td class="arabic">{{ $row['Customer Name'] }}</td>
                    <td>{{ $row['Invoice Date'] }}</td>
                    <td class="text-right">{{ $row['Discount'] }}</td>
                    <td class="text-right">{{ $row['Taxable Amount'] }}</td>
                    <td class="text-right">{{ $row['Tax Amount'] }}</td>
                    <td class="text-right">{{ $row['Total Amount'] }}</td>
                </tr>

                @php
                    $totalDiscount += (float) str_replace(',', '', $row['Discount']);
                    $totalTaxable += (float) str_replace(',', '', $row['Taxable Amount']);
                    $totalTax += (float) str_replace(',', '', $row['Tax Amount']);
                    $totalAmount += (float) str_replace(',', '', $row['Total Amount']);
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>{{ number_format($totalDiscount, 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalTaxable, 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalTax, 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; font-size: 8px; text-align: center;">
        Generated on: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}
    </div>
</body>

</html>
