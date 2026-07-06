<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sales Tax Reports</title>
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
        }

        th,
        td {
            padding: 8px;
            text-align: left;
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
    </style>
</head>

<body>
    <h3>{{ config('app.name') }} - Sales Tax Report</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice Number</th>
                <th>Customer Name</th>
                <th>Date</th>
                <th class="text-right">Taxable Amount</th>
                <th class="text-right">Tax Amount</th>
                <th class="text-right">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $taxableTotal = 0;
                $taxTotal = 0;
                $totalAmount = 0;
            @endphp

            @foreach ($exportData as $row)
                <tr>
                    <td class="text-center">{{ $row['#'] }}</td>
                    <td>{{ $row['Invoice Number'] }}</td>
                    <td>{{ $row['Customer Name'] }}</td>
                    <td>{{ $row['Date'] }}</td>
                    <td class="text-right">{{ $row['Taxable Amount'] }}</td>
                    <td class="text-right">{{ $row['Tax Amount'] }}</td>
                    <td class="text-right">{{ $row['Total Amount'] }}</td>
                </tr>

                @php
                    $taxableTotal += (float) str_replace(',', '', $row['Taxable Amount']);
                    $taxTotal += (float) str_replace(',', '', $row['Tax Amount']);
                    $totalAmount += (float) str_replace(',', '', $row['Total Amount']);
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>{{ number_format($taxableTotal, 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($taxTotal, 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; font-size: 10px; text-align: center;">
        Generated on: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}
    </div>
</body>

</html>
