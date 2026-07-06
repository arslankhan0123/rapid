@php
    $report_date = \Carbon\Carbon::createFromFormat('Y-m', $salaryGenerate->salary_month)->format('F Y');
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Details</title>
    <style>
        @page {
            size: A4 landscape; /* Set the page size to A4 and change the orientation to landscape */
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
                <h2 style="font-weight: bold;text-align:center;color:blue;">Salary Sheet For {{$report_date }}</h2>
            </td>
        </tr>
    </table>
    <br>
    <table id="salaryTable" class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th style="text-align: center;">{{ __('messages.employee_salaries.id') }}</th>
                <th>
                    <div>Employee<br>Name</div>
                </th>
                <th>{{ __('messages.employee_salaries.basic_salary') }}</th>
                <th>{{ __('messages.employee_salaries.bonuses') }}</th>
                <th>{{ __('messages.employee_salaries.total_allowances') }}</th>
                <th>{{ __('messages.employee_salaries.gross_salary') }}</th>
                <th>{{ __('messages.employee_salaries.salary_advance') }}</th>
                <th>{{ __('messages.employee_salaries.loan') }}</th>
                <th>{{ __('messages.employee_salaries.total_deduction') }}</th>
                <th>{{ __('messages.employee_salaries.net_salary') }}</th>
                <th>{{ __('messages.employee_salaries.signature') }}</th>
                <th>{{ __('messages.employee_salaries.fingerprint') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sheets as $sheet)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <div style="width: 100px;text-align:center">{{ $sheet->employee->iqama_no }}</div>
                    </td>
                    <td>
                        <div>{{ $sheet->employee->name }}</div>
                    </td>
                    <td>{{ number_format($sheet->basic_salary, 2) }}</td>
                    <td>{{ number_format($sheet->total_bonus, 2) }}</td>
                    <td>{{ number_format($sheet->total_allowances, 2) }}</td>
                    <td>{{ number_format($sheet->gross_salary, 2) }}</td>
                    <td>{{ number_format($sheet->salary_advance, 2) }}</td>
                    <td>{{ number_format($sheet->loan, 2) }}</td>
                    <td>{{ number_format($sheet->total_deduction, 2) }}</td>
                    <td>{{ number_format($sheet->net_salary, 2) }}</td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="thank-you">
        <p>Makkah Al Mukarramah . Omaima Bint Al Khattab Street . Tel: 012538109 . P.O.Box 3177 Code 24241 . Kingdom of
            Saudi Arabia</p>
    </div>
</body>

</html>
