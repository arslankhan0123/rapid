<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sales Item Report</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            direction: ltr;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
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

        tfoot td {
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <h3 style="text-align: center;">{{ config('app.name') }} - Sales Item Report</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice Number</th>
                <th>Customer Name</th>
                <th>Invoice Date</th>
                <th>Item</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Rate</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $quantityTotal = 0;
                $rateTotal = 0;
                $totalAmount = 0;
            @endphp

            @foreach ($exportData as $row)
                <tr>
                    <td class="text-center">{{ $row['#'] }}</td>
                    <td>{{ $row['Invoice Number'] }}</td>
                    <td class="arabic">{{ $row['Customer Name'] }}</td>
                    <td>{{ $row['Invoice Date'] }}</td>
                    <td>{{ $row['Item'] }}</td>
                    <td class="text-right">{{ $row['Quantity'] }}</td>
                    <td class="text-right">{{ $row['Rate'] }}</td>
                    <td class="text-right">{{ $row['Total'] }}</td>
                </tr>
                @php
                    $quantityTotal += (float) str_replace(',', '', $row['Quantity']);
                    $rateTotal += (float) str_replace(',', '', $row['Rate']);
                    $totalAmount += (float) str_replace(',', '', $row['Total']);
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>{{ number_format($quantityTotal, 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($rateTotal, 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
