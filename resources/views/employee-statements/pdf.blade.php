<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Statements</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 20px;
        }

        .header img {
            width: 20%;
        }

        .header-content {
            text-align: center;
            width: 65%;
        }

        .table-bordered {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th,
        td {
            border: 1px solid #dee2e6;
            padding: 3px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .amount-summary {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .summary-section {
            width: 30%;
        }

        .thank-you {
            text-align: center;
            margin: 20px 0;
        }

        .address-section {
            margin: 10px 0;
        }

        /* Flex layout for amount summary */
        .summary-container {
            display: flex;
            justify-content: space-between;
        }

        .summary-left {
            width: 45%;
        }

        .summary-center {
            width: 10%;
            text-align: center;
        }

        .summary-right {
            width: 45%;
        }

        .bank-details {
            margin-top: 0px;
            padding-top: 10px;
        }
    </style>
</head>

<body>




    <table style="width: 100%; table-layout: fixed;">
        <tr>
            <td style="width: 30%;">
                {{-- <img src="data:image/png;base64,{{ $com_logo }}" style="width: 60%;padding:10%;"> --}}
            </td>
            <td style="width: 100%;">
                <h4 style="font-weight: bold; text-align:center;">Manara for General Contracting Est.</h4>
                <p style="text-align:center;">
                    C.R. 4031251746 &nbsp; | &nbsp; Mobile: +966 562015468 <br>
                    Email: <a href="mailto:info@manaraest.com">info@manaraest.com</a> &nbsp; | &nbsp;
                    Website: <a href="https://www.manaraest.com" target="_blank">www.manaraest.com</a>
                </p>

            </td>
        </tr>
    </table>
    <br>
    <table class="table-bordered tblcustomer" style="width: 100%;">
        <tr>
            <td><strong>Customer Number</strong><br>{{ $customer->code ?? '' }}</td>
            <td><strong>Customer Name</strong><br>{{ $customer->company_name ?? '' }}</td>
            <td><strong>Statement Date</strong><br>{{ \Carbon\Carbon::now()->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td><strong>Email Address</strong><br>{{ $customer->email ?? '' }}</td>
            <td colspan="2"><strong>Address</strong><br>
                @php
                    $addressParts = array_filter([
                        $customer->customerCountry->name ?? '',
                        $customer->address->customerState->name ?? '',
                        $customer->address->city ?? '',
                        $customer->address->zip ?? '',
                        $customer->address->street ?? '',
                    ]);
                @endphp
                {{ implode(', ', $addressParts) }}
            </td>
        </tr>

        <tr>

            {{-- <td><strong>Payment Mode</strong><br>{{ $paymentModes[$payment_mode] ?? 'N/A' }}</td> --}}
        </tr>
    </table>

    <br>
    <table class="table-bordered tblcustomer" style="width: 100%;">
        <thead>
            <tr>
                <th>Invoice<br>Date</th>
                <th>Invoice<br>Number</th>
                <th>Receipt<br>Date</th>
                <th>Month</th>
                <th>Project Name</th>
                <th class="text-right">Amount</th>
                <th class="text-right">Received</th>
                <th class="text-right">Balance</th>

            </tr>
        </thead>
        <tbody>
            @php
                $netAmount = 0; // Initialize net amount variable
                $received = 0;
                $balance = 0;
            @endphp
            @foreach ($pdfData['statements'] as $statement)
                @php
                    $netAmount += $statement->net_amount;
                    $received += $statement->paid_amount;
                    $balance += $statement->balance_due;
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($statement->posted_at)->format('d-m-Y') }}</td>
                    <td>{{ $statement->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($statement->updated_at)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($statement->month)->format('M Y') }}</td>
                    <td>{{ $statement->project->project_name ?? '' }}</td>
                    <td class="text-right">{{ number_format($statement->net_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($statement->paid_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($statement->balance_due, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="padding-top: 10px;">
                    <strong>Bank Details</strong><br>
                    {!! nl2br(e($bank->value ?? '')) !!}
                </td>
                <td class="text-right" style="vertical-align: top;"><strong>Total</strong> </td>
                <td class="text-right" style="vertical-align: top;">
                    <div style="height: 10px; padding: 0; margin: 0;">
                        <strong>{{ number_format($netAmount, 2) }}</strong>
                    </div>
                </td>
                <td class="text-right" style="vertical-align: top;"><strong>{{ number_format($received, 2) }}</strong></td>
                <td class="text-right" style="vertical-align: top;"><strong>{{ number_format($balance, 2) }}</strong></td>

            </tr>
        </tbody>
    </table>


    <div class="thank-you">
        <p style="font-weight: 600;">THANK YOU FOR YOUR BUSINESS!!</p>
        <p style="font-weight: bold;">Makkah Al Mukarramah . Omaima Bint Al Khattab Street . Tel: 012538109 . P.O.Box
            3177 Code 24241 . Kingdom
            of
            Saudi Arabia</p>



</body>

</html>
