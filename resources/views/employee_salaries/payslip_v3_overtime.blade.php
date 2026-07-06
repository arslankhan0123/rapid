@php

    use Carbon\Carbon;
    $com_logo = '/company_logo_color.png';

    $format = $settings['print_format'] ?? 1;
    if ($format == 2) {
        $com_logo = '/company_logo_color_ran.png';
    }

    $baseImagePath = public_path('img/company');
    // Company logo
    $imagePath = $comLogoPath;
    $imageData = $imagePath && file_exists($imagePath) ? file_get_contents($imagePath) : '';
    $base64 = base64_encode($imageData);
    $company_logo = 'data:image/png;base64,' . $base64; // Ensure correct format
    $bgColor = '#fff7f2';
    $bColor = '#e2e2e2';

    // Parse the salary month and get the total days in the month
    $salaryMonth = Carbon::parse($salarySheet->salaryGenerate->salary_month);
    $totalDaysInMonth = $salaryMonth->daysInMonth;

    // Define daily working hours
    $dailyWorkingHours = 8;

    // Calculate total working hours for the month
    $totalWorkingHoursInMonth = $totalDaysInMonth * $dailyWorkingHours;

    // Calculate Worked Days
    $workedAndOvertimeHours = $salarySheet->worked_hours + $salarySheet->overtime_hours;
    $workedDays = $workedAndOvertimeHours / $dailyWorkingHours;

    // Calculate LOP (Loss of Pay) Days
    $lopHours = $salarySheet->absence_hours; // Total absence hours
    $lopDays = $lopHours / $dailyWorkingHours;

@endphp


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            font-size: 10pt;
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
            width: 2.5cm;
            height: 2.62cm;
        }

        .left div {
            width: 2.5cm;
            height: 2.62cm;
            background-image: url('{{ $company_logo }}');
            background-size: 100%;
            /* Ensures the image covers the entire container */
            background-position: center;
            /* Centers the image */
            background-repeat: no-repeat;
            /* Prevents tiling */
        }

        .content_header .left {
            text-align: left;
            width: 20%;
        }

        .content_header .middle {
            text-align: center;
            width: 60%;
        }

        .content_header .right {
            text-align: right;
            width: 20%;
        }

        .content_header img {
            max-width: 100%;
            max-height: 50px;
        }

        .content_header h2 {
            margin: 0;
            font-size: 18px;
        }

        .content_header .net_pay {
            font-weight: bold;
            font-size: 16px;
        }

        .content_body {

            margin-left: .8cm;
            margin-right: .8cm;
        }

        .employee_info {
            border-collapse: collapse;
            width: 100%;
            /* Adjust the width as needed */
        }

        .employee_info th,
        .employee_info td {
            padding: 1px;
            text-align: left;
        }

        .employee_info th {}

        .employee_info .header-row {}

        .bColor {
            border: .05cm solid #e2e2e2 !important;
        }

        .bgColor {
            background: #fff7f2 !important;
        }

        .salary_info {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid {{ $bColor }};
            /* Add overall table border */
            font-family: Arial, sans-serif;
            /* Set font for better readability */
            font-size: 14px;
            margin-top: 0.60cm;
            /* Adjust font size as needed */
        }

        .salary_info th,
        .salary_info td {
            border: 1px solid {{ $bColor }};
            padding: 8px;
            text-align: left;

        }

        .salary_info th {
            background: {{ $bgColor }};
            text-align: center;
        }

        .salary_info .total-row {
            font-weight: bold;
            /* Bold font for total row */
        }

        .salary_info .amount-in-words {
            font-style: italic;
            /* Italic font for amount in words */
        }

        .text-right {
            text-align: right !important;
        }

        .textLeftPadding {
            padding-left: 30px !important;
        }
    </style>
</head>

<body>


    <div class="contents">
        <div class="content_header">
            <div class="left">
                <div>
                </div>
            </div>
            <div class="middle">
                <h1 style="font-size: 20pt;">Payslip (Over Time)</h1>
                <p></p>
                <p>{{ html_entity_decode($settings['company'] ?? '') }}<br>{{ $salarySheet->branch?->name ?? '' }}</p>
            </div>
            <div class="right">
                <h1 style="font-size: 20pt;">NET PAY<br> {{ number_format($salarySheet->total_overtimes ?? 0, 2) }}</h1>
            </div>
        </div>
        <div class="content_body">
            <table class="employee_info">
                <tr>
                    <th colspan="2" class="header-row">Employee Id</th>
                    <th colspan="2" class="header-row textLeftPadding">Employee Name</th>
                    <th colspan="2" class="header-row textLeftPadding">Date of Joining</th>
                    <th colspan="2" class="header-row textLeftPadding">Designation</th>
                </tr>
                <tr>
                    <td colspan="2">{{ $salarySheet->employee?->iqama_no ?? 'N/A' }}</td>
                    <td colspan="2" class="textLeftPadding">{{ $salarySheet->employee?->name ?? 'N/A' }}</td>
                    <td colspan="2" class="textLeftPadding">
                        {{ $salarySheet->employee?->join_date ? $salarySheet->employee?->join_date->format('d-m-Y') : 'N/A' }}
                    </td>
                    <td colspan="2" class="textLeftPadding">
                        {{ $salarySheet->employee?->designation?->name ?? 'N/A' }}</td>
                </tr>
                <br>
                <tr>
                    <th colspan="2" class="header-row">Department</th>
                    <th colspan="2" class="header-row textLeftPadding">Bank Account</th>
                    <th colspan="2" class="header-row textLeftPadding">Bank Name</th>
                    <th colspan="2" class="header-row textLeftPadding">Pay Period</th>
                </tr>
                <tr>
                    <td colspan="2">{{ $salarySheet->employee?->department?->name ?? 'N/A' }}</td>
                    <td colspan="2" class="textLeftPadding">{{ $salarySheet->employee?->bank_account_no ?? 'N/A' }}
                    </td>
                    <td colspan="2" class="textLeftPadding">{{ $salarySheet->employee?->bank_name ?? 'N/A' }}</td>
                    <td colspan="2" class="textLeftPadding">
                        {{ \Carbon\Carbon::parse($salarySheet->salaryGenerate->salary_month)->format('F Y') }}</td>
                </tr>
                <br>
                <tr>
                    <th colspan="2" class="header-row">Worked Days</th>
                    <th colspan="2" class="header-row textLeftPadding">Overtime Hours</th>
                    <th colspan="2" class="header-row textLeftPadding">LOP Days</th>
                    <th colspan="2" class="header-row textLeftPadding">Payment Date</th>
                </tr>
                <tr>
                    <td colspan="2">{{ number_format($workedDays, 2) ?? 'N/A' }} </td>
                    <td colspan="2" class="textLeftPadding">
                        {{ number_format($salarySheet->overtime_hours ?? 0, 2) }}</td>
                    <td colspan="2" class="textLeftPadding">{{ number_format($lopDays, 2) ?? 'N/A' }} </td>
                    <td colspan="2" class="textLeftPadding">
                        {{ \Carbon\Carbon::parse($salarySheet->salaryGenerate->salary_month)->day . \Carbon\Carbon::parse($salarySheet->salaryGenerate->salary_month)->format('S F Y') }}
                    </td>
                </tr>
            </table>

            <table class="salary_info">
                <tr>
                    <th colspan="2" class="header-row">Description</th>
                    <th class="header-row earnings-column">Earnings</th>

                </tr>



                <tr>
                    <td colspan="2">Overtime Pay</td>
                    <td class="earnings-column text-right"> {{ number_format($salarySheet->total_overtimes ?? 0, 2) }}
                    </td>

                </tr>

                <tr>
                    <td colspan="2" style="max-width: 150px; word-wrap: break-word;">
                        <p><strong class="h5">Amount in Words</strong></p>
                        <p class="text-break" style="font-size:12pt;">{{ ucfirst($words) }}</p>
                    </td>

                    <td style="text-align:right;">
                        <p><Strong style="font-size:14pt;"> Net Pay</Strong></p>

                        <p><Strong style="font-size:14pt;">
                                {{ number_format($salarySheet->total_overtimes ?? 0, 2) }}</Strong></p>
                    </td>

                </tr>


            </table>
            <table style="margin: 0 auto; text-align: center; width: 100%; table-layout: fixed;margin-top:70px;">
                <tr>
                    <td style="padding: 10px;">Prepared By ________________</td>
                    <td style="padding: 10px;">Approved By ________________</td>
                    <td style="padding: 10px;">Received By ________________</td>
                </tr>
            </table>

        </div>

    </div>
</body>

</html>
