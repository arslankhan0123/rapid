<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Credit Note Reports</title>
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
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        th,
        td {
            padding: 5px;
            text-align: left;
            word-wrap: break-word;
            overflow: hidden;
            border: 1px solid #ddd;
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
            direction: rtl;
            unicode-bidi: embed;
        }

        /* Set specific widths for columns */
        th:nth-child(1),
        td:nth-child(1) {
            width: 5%;
        }

        /* # */
        th:nth-child(2),
        td:nth-child(2) {
            width: 15%;
        }

        /* Credit Note Number */
        th:nth-child(3),
        td:nth-child(3) {
            width: 20%;
        }

        /* Customer Name */
        th:nth-child(4),
        td:nth-child(4) {
            width: 12%;
        }

        /* Credit Note Date */
        th:nth-child(5),
        td:nth-child(5) {
            width: 10%;
        }

        /* Discount */
        th:nth-child(6),
        td:nth-child(6) {
            width: 12%;
        }

        /* Taxable Amount */
        th:nth-child(7),
        td:nth-child(7) {
            width: 10%;
        }

        /* Tax Amount */
        th:nth-child(8),
        td:nth-child(8) {
            width: 12%;
        }

        /* Total Amount */
        th:nth-child(9),
        td:nth-child(9) {
            width: 10%;
        }

        /* Status */
    </style>
</head>

<body>
    <h3>{{ config('app.name') }} - Credit Note Report</h3>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Credit Note Number</th>
                <th>Customer Name</th>
                <th>Credit Note Date</th>
                <th>Discount</th>
                <th>Taxable Amount</th>
                <th>Tax Amount</th>
                <th>Total Amount</th>
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
                    <td>{{ $row['Credit Note Number'] }}</td>
                    <td class="arabic">{{ $row['Customer Name'] }}</td>
                    <td>{{ $row['Credit Note Date'] }}</td>
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
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; font-size: 10px; text-align: center;">
        Generated on: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}
    </div>
</body>

</html>
