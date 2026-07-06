<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Check Print</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            width: 2.52cm;
        }

        .date {
            position: absolute;
            top: 0.36cm;
            left: 16.55cm;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            width: 105px;
        }

        .issue_place {
            position: absolute;
            top: 1.12cm;
            left: 17.46cm;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: -0.5px;
            width: 65px;
            height: 40px;
            text-align: center;

        }

        .issue_name {
            position: absolute;
            top: 2.5cm;
            left: 3.5cm;
            font-size: 16px;
            font-weight: bold;
            height: 40px;

        }

        .amount-text {
            position: absolute;
            top: 3.4cm;
            left: 2.8cm;
            font-size: 15px;
            font-weight: bold;
            height: 60px;
            text-transform: capitalize;
            width: 420px;

            line-height: 30px;

        }

        .amount-box {
            position: absolute;
            top: 3.78cm;
            left: 16.6cm;
            font-size: 16px;
            font-weight: bold;
            height: 32px;
            letter-spacing: -0.5px;
            line-height: 30px;
            width: 135px;

        }
    </style>
</head>

<body>


    {{-- Date --}}
    <div class="date">
        {{ \Carbon\Carbon::parse($check->date)->format('Y/m/d') }}
    </div>


    {{-- Payee Name --}}
    <div class="issue_place">
        {{ $check->issue_place ?? '' }}
    </div>

    <div class="issue_name">
        {{ $check->issue_name ?? '' }}
    </div>
    {{-- Amount in Words --}}
    <div class="amount-text">
        {{ $words ?? '' }} Only


    </div>

    {{-- Numeric Amount --}}
    <div class="amount-box">
        {{ number_format($check->amount, 2) }} SR
    </div>




</body>

</html>
