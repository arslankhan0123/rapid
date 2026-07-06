<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        @page {
            size: A4 ;
            /* Set the page size to A4 and change the orientation to landscape */
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
                <img src="data:image/png;base64,{{ $com_logo }}" style="width: 60%;padding:10%;">
            </td>
            <td style="width: 100%;">
                <h4 style="font-weight: bold; text-align:center;">Manara for General Contracting Est.</h4>
                <p style="text-align:center;">
                    C.R. 4031251746 &nbsp; | &nbsp; Mobile: +966 562015468 <br>
                    Email: <a href="mailto:info@manaraest.com">info@manaraest.com</a> &nbsp; | &nbsp;
                    Website: <a href="https://www.manaraest.com" target="_blank">www.manaraest.com</a>
                </p>
                <h2 style="text-align: center;">{{ __('messages.employee_salaries.payslip') }} (
                    {{ str_pad($salarySheet->employee->iqama_no, 6, '0', STR_PAD_LEFT) }})</h2>
            </td>
        </tr>
    </table>
    <br>
    <table class="table table-bordered table-custom">
        <tr>
            <th>{{ __('messages.employee_salaries.employee_name') }}</th>
            <td>{{ $salarySheet->employee->name }}</td>
            <th>{{ __('messages.employee_salaries.id') }}</th>
            <td>{{ $salarySheet->employee->iqama_no }}</td>

        </tr>
        <tr>
            <th>{{ __('messages.employee_salaries.position') }}</th>
            <td>{{ $salarySheet->employee->designation->name ?? null }}</td>
            <th>{{ __('messages.employee_salaries.from') }}</th>
            <td>{{ \Carbon\Carbon::parse($salarySheet->salaryGenerate->salary_month)->startOfMonth()->format('Y-m-d') }}
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.employee_salaries.contact') }}</th>
            <td>{{ $salarySheet->employee->phone }}</td>
            <th>{{ __('messages.employee_salaries.to') }}</th>
            <td> {{ \Carbon\Carbon::parse($salarySheet->salaryGenerate->salary_month)->endOfMonth()->format('Y-m-d') }}
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.employee_salaries.address') }}</th>
            <td>{{ $salarySheet->employee->city }}, {{ $salarySheet->employee->state }}</td>
            <th>{{ __('messages.employee_salaries.approved_date') }}</th>
            <td>{{ \Carbon\Carbon::parse($salarySheet->salaryGenerate->approved_date)->format('F d, Y') }}
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.employee_salaries.total_working_hours') }}</th>
            <td></td> <!-- Example value -->
            <th>{{ __('messages.employee_salaries.worked_hours') }}</th>
            <td></td> <!-- Example value -->
        </tr>
        <tr>
            <th>{{ __('messages.employee_salaries.basic_salary') }}</td>
            <td>{{ number_format($salarySheet->basic_salary, 2) }}</td>

            <th>{{ __('messages.employee_salaries.gross_salary') }}</th>
            <td>{{ number_format($salarySheet->gross_salary, 2) }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.employee_salaries.month') }}</th>
            <td>{{ \Carbon\Carbon::parse($salarySheet->salaryGenerate->salary_month)->format('F, Y') }}
            </td>
        </tr>

    </table>


    <div style="width: 100%; height:auto; padding:20px;">

        <!-- Right Aligned Table -->
        <div style="width: 30%; float: right;">
            <table class="table table-bordered table-custom" style="width: 90%;">
                <thead>
                    <tr>
                        <th>{{ __('messages.employee_salaries.description') }}</th>
                        <th>{{ __('messages.employee_salaries.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ __('messages.employee_salaries.total_deduction') }}</td>
                        <td>{{ number_format($salarySheet->total_deduction, 2) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('messages.employee_salaries.overtime') }}</td>
                        <td>{{ number_format($salarySheet->total_bonus, 2) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('messages.employee_salaries.salary_advance') }}</td>
                        <td>{{ number_format($salarySheet->salary_advance, 2) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('messages.employee_salaries.total_allowances') }}</td>
                        <td>{{ number_format($salarySheet->total_allowances, 2) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('messages.employee_salaries.total_loan') }}</td>
                        <td>{{ number_format($salarySheet->loan, 2) }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.employee_salaries.net_salary') }}</th>
                        <th>{{ number_format($salarySheet->net_salary, 2) }}</th>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Left Aligned Authorized By -->
        <div style="width: 60%; float: left; text-align: left;">

        </div>

        <div style="clear: both;"></div> <!-- Clear floats -->
    </div>
    <div class="authorized-by" style="width:200px;text-align:center;">
        <hr>
        <p>Authorized by</p>
    </div>
    <!-- Bottom Text -->
    <div class="thank-you" style="text-align: center; margin-top: 10px;">
        <p>Makkah Al Mukarramah . Omaima Bint Al Khattab Street . Tel: 012538109 . P.O.Box 3177 Code 24241 . Kingdom of
            Saudi Arabia</p>
    </div>


</body>

</html>
