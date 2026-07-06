@php
    use Carbon\Carbon;
    $report_date = \Carbon\Carbon::createFromFormat('Y-m', $salaryGenerate->salary_month)->format('F Y');

    $baseImagePath = public_path('img/company');
    // Company logo
    $imagePath = $baseImagePath . '/company_logo_color.png';
    $imageData = file_get_contents($imagePath);
    $base64 = base64_encode($imageData);
    $company_logo = 'data:image/png;base64,' . $base64; // Ensure correct format
    $bgColor = '#fff7f2';
    $bColor = '#e2e2e2';
    $totalWd = 0;
    $totalCTC = 0;
    $totalDeductions = 0;

@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Details</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0.3cm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 7.5pt;
        }

        .content_header {
            display: table;
            width: 100%;
            background-color: {{ $bgColor }};
            border: 2px solid {{ $bColor }};
            padding: 5px;
            table-layout: fixed;
            border-collapse: collapse;
        }

        /* Company name styling to prevent overlap */
        .company-name {
            font-size: 14pt !important;
            line-height: 1.1 !important;
            color: #f7996e;
            word-wrap: break-word;
            white-space: normal;
            margin: 0;
            padding: 0;
            max-height: 1.8cm;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            font-weight: bold;
            text-align: center;
        }

        .salary-title {
            font-size: 14pt !important;
            font-weight: bold;
            color: #3b6b67;
            margin: 3px 0;
            line-height: 1.1;
            text-align: center;
        }

        .branch-name {
            font-size: 9pt;
            margin: 3px 0;
            color: #666;
            line-height: 1.1;
            text-align: center;
            font-weight: normal;
        }

        .content_header .left,
        .content_header .middle,
        .content_header .right {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            overflow: hidden;
            padding: 3px;
        }

        .left {
            width: 20%;
            height: 2cm;
            text-align: left;
            padding-left: 10px;
        }

        .left div {
            width: 2.5cm;
            height: 2cm;
            background-image: url('{{ $company_logo }}');
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
        }

        .middle {
            width: 50%;
            padding: 0 10px;
            text-align: center;
        }

        .right {
            width: 30%;
            padding-right: 10px;
            text-align: right;
        }

        .right h1 {
            font-size: 14pt;
            margin: 0;
            line-height: 1.2;
            color: #3b6b67;
        }

        .right h1 br {
            display: block;
            content: "";
            margin: 3px 0;
        }

        .bColor {
            border: .05cm solid #e2e2e2 !important;
        }

        .bgColor {
            background: #fff7f2 !important;
        }

        .salary_sheet_table {
            border-collapse: collapse;
            width: 100%;
            margin: 8px auto 0 auto;
            font-family: Arial, sans-serif;
            font-size: 6pt;
        }

        .salary_sheet_table th,
        .salary_sheet_table td {
            border: 1px solid #e2e2e2;
            padding: 2px 1px;
            text-align: center;
            vertical-align: middle;
        }

        .salary_sheet_table th {
            background-color: {{ $bgColor }};
            font-weight: bold;
            padding: 3px 1px;
        }

        .salary_sheet_table .header-row {
            background-color: {{ $bgColor }};
        }

        .salary_sheet_table .earnings-column {
            background-color: {{ $bgColor }}2;
        }

        .salary_sheet_table .deductions-column {
            background-color: {{ $bgColor }};
        }

        .salary_sheet_table .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .salary_sheet_table .amount-in-words {
            font-style: italic;
            text-align: left;
            padding-left: 10px;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        .textH {
            color: #ff7038 !important;
        }

        .contents {
            width: 100%;
            padding: 0;
            margin: 0;
        }

        /* For better PDF rendering */
        * {
            box-sizing: border-box;
        }

        /* Ensure proper spacing */
        .content_header>div {
            min-height: 2.62cm;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .middle>* {
            margin: 2px 0;
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
                <h1 class="company-name">{{ $settings['company'] ?? '' }}</h1>
                <p>
                    <span class="salary-title">SALARY SHEET</span><br>
                    <span class="branch-name">{{ $salaryGenerate->branch?->name ?? '' }}</span>
                </p>
            </div>
            <div class="right">
                <h1>Total<br>{{ number_format($sheets->sum('net_salary'), 2) }}</h1>
            </div>
        </div>
        <div class="content_body">
            <table class="salary_sheet_table">
                <tr>
                    <th rowspan="2" class="header-row">SL</th>
                    <th rowspan="2" class="header-row text-left" style="white-space:nowrap;">Emp Id</th>
                    <th rowspan="2" class="header-row text-left" style="white-space:nowrap;">Employee Name</th>
                    <th rowspan="2" class="header-row text-left" style="white-space:nowrap;">Iqama</th>
                    <th rowspan="2" class="header-row text-left" style="white-space:nowrap;">Designation</th>
                    <th rowspan="2" class="header-row text-left" style="white-space:nowrap;">Working Days</th>
                    <th colspan="5" class="header-row earnings-column">Earnings</th>
                    <th colspan="7" class="header-row deductions-column">DEDUCTIONS</th>
                    <th rowspan="2" class="header-row">Net</th>
                    <th rowspan="2" class="header-row" style="padding-left:10px;padding-right:10px;">Finger</th>
                    <th rowspan="2" class="header-row" style="padding-left:10px;padding-right:10px;">Signature</th>

                </tr>
                <tr>
                    <th>Basic</th>
                    <th>Bonus</th>
                    <th>Allowances</th>
                    <th>Overtime</th>
                    <th>Gross</th>
                    <th>Advance</th>
                    <th>Loan</th>
                    <th>Absent</th>
                    <th>Insurance</th>
                    <th>Allowances</th>
                    <th>Deduction</th>
                    <th>Total</th>
                </tr>
                @php
                    $totalAllowanceDeductions = 0;
                    $totalManualDeductions = 0;
                    $totalAllDeductions = 0;
                    $totalWorkingDays = 0;
                @endphp
                @foreach ($sheets as $sheet)
                    @php

                        $salaryMonth = Carbon::parse($sheet->salaryGenerate->salary_month);
                        $totalDaysInMonth = $salaryMonth->daysInMonth;
                        $dailyWorkingHours = 8;
                        // Calculate total working hours for the month
                        $totalWorkingHoursInMonth = $totalDaysInMonth * $dailyWorkingHours;

                        // Calculate Worked Days
                        $workingDays = $sheet->employee->attendance->where('hours', '>', 0)->count();
                        $totalWorkingDays += $workingDays;

                        $perDayAllowance = $sheet->total_allowances / 30;
                        $perHourAllowance = $perDayAllowance / 8;
                        $allowanceDeduction = $perHourAllowance * $sheet->absence_hours;
                        $totalAllowanceDeductions += $allowanceDeduction;

                        $manualDeduction = $sheet->total_deduction - $sheet->hourly_deduction;
                        $totalManualDeductions += $manualDeduction;

                        $rowTotalDeduction = $sheet->salary_advance + $sheet->loan + $sheet->hourly_deduction + $sheet->total_insurance + $allowanceDeduction + $manualDeduction;
                        $totalAllDeductions += $rowTotalDeduction;

                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-left" style="white-space:nowrap;">{{ $sheet->employee->code }}</td>
                        <td class="text-left" style="white-space:nowrap;">{{ $sheet->employee->name }}</td>
                        <td class="text-left" style="white-space:nowrap;">{{ $sheet->employee->iqama_no }}</td>
                        <td class="text-left" style="white-space:nowrap;">{{ $sheet->employee?->designation?->name ?? '' }}</td>

                        <td style="white-space:nowrap;">{{ $workingDays }}</td>

                        <!-- Earnings Columns -->
                        <td class=" text-right">{{ number_format($sheet->basic_salary, 2) }}</td>
                        <td class=" text-right">{{ number_format($sheet->total_bonus, 2) }}</td>
                        <td class=" text-right">{{ number_format($sheet->total_allowances, 2) }}</td>
                        <td class=" text-right">{{ number_format($sheet->total_overtimes, 2) }}</td>
                        <td class=" text-right">{{ number_format($sheet->basic_salary + $sheet->total_bonus + $sheet->total_allowances + $sheet->total_overtimes, 2) }}</td>

                        <!-- Deductions Columns -->
                        <td class=" text-right">{{ number_format($sheet->salary_advance, 2) }}</td>
                        <td class=" text-right">{{ number_format($sheet->loan, 2) }}</td>
                        <td class=" text-right">{{ number_format($sheet->hourly_deduction, 2) }}</td>
                        <td class=" text-right">{{ number_format($sheet->total_insurance, 2) }}</td>
                        <td class=" text-right">{{ number_format($allowanceDeduction, 2) }}</td>
                        <td class=" text-right">{{ number_format($manualDeduction, 2) }}</td>
                        <td class=" text-right">{{ number_format($rowTotalDeduction, 2) }}</td>

                        <td class="text-right">{{ number_format($sheet->net_salary, 2) }}</td>
                        <td></td> <!-- Placeholder for Finger -->
                        <td></td> <!-- Placeholder for Signature -->
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5">Total</td>
                    <td>{{ $totalWorkingDays }}</td>
                    
                    <td class="text-right">{{ number_format($sheets->sum('basic_salary'), 2) }}</td>
                    <td class="text-right">{{ number_format($sheets->sum('total_bonus'), 2) }}</td>
                    <td class="text-right">{{ number_format($sheets->sum('total_allowances'), 2) }}</td>
                    <td class="text-right">{{ number_format($sheets->sum('total_overtimes'), 2) }}</td>
                    <td class="text-right">{{ number_format($sheets->sum('basic_salary') + $sheets->sum('total_bonus') + $sheets->sum('total_allowances') + $sheets->sum('total_overtimes'), 2) }}</td>
                    
                    <td class="text-right">{{ number_format($sheets->sum('salary_advance'), 2) }}</td>
                    <td class="text-right">{{ number_format($sheets->sum('loan'), 2) }}</td>
                    <td class="text-right">{{ number_format($sheets->sum('hourly_deduction'), 2) }}</td>
                    <td class="text-right">{{ number_format($sheets->sum('total_insurance'), 2) }}</td>
                    <td class="text-right">{{ number_format($totalAllowanceDeductions, 2) }}</td>
                    <td class="text-right">{{ number_format($totalManualDeductions, 2) }}</td>
                    <td class="text-right">{{ number_format($totalAllDeductions, 2) }}</td>
                    
                    <td class="text-right">{{ number_format($sheets->sum('net_salary'), 2) }}</td>
                    <td></td> <!-- Placeholder for Finger -->
                    <td></td> <!-- Placeholder for Signature -->
                </tr>

            </table>
            <table style="margin: 0 auto; text-align: center; width: 100%; table-layout: fixed;margin-top:70px;">
                <tr>
                    <td style="padding: 10px;">Prepared By ________________</td>
                    <td style="padding: 10px;"> </td>
                    <td style="padding: 10px;">Approved By ________________</td>
                </tr>
            </table>

        </div>
    </div>

</body>

</html>
